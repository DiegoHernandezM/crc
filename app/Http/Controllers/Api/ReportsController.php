<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiResponses;
use App\Http\Resources\AssociateCollection;
use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Associate;
use App\Models\Checkin;
use App\Models\ProductivitySorter;
use App\Models\Shift;
use App\Models\WaveProductivitySorter;
use App\Models\PickingBonus;
use App\Repositories\ReportRepository;
use Aws\kendra\kendraClient;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ReportsController extends Controller
{

    protected $mAssociates;
    protected $cReportRepo;
    protected $mCheckin;

    public function __construct()
    {
        $this->mAssociates = new Associate();
        $this->cReportRepo = new ReportRepository();
        $this->mCheckin = new Checkin();
    }

    public function shiftAssociate()
    {
        $this->authorize('Reportes.Asistencias');
        $associates = Associate::where(function ($q) {
            if (Auth::user()->area_id != null) {
                return $q->where('area_id', Auth::user()->area_id);
            }
        })->get();
        return Inertia::render('Reports/Shifts/Index', [
            'filters' => Request::all('search', 'trashed'),
            'associates' => new AssociateCollection(
                $associates
            ),
        ]);
    }

    public function getAssociateRegisters(Request $oRequest)
    {
        $this->authorize('Reportes.Asistencias');
        $associate = $this->mAssociates->join('areas', 'areas.id', '=', 'associates.area_id')->join('shifts', 'shifts.id', '=', 'associates.shift_id')
            ->where('associates.id', $oRequest->id)
            ->select('associates.*', 'areas.name as area', 'shifts.name as shift')->first()->getPictureAttribute(false);

        $checks = $this->mCheckin->where('associate_id', $associate['id'])
            ->where(function ($q) use ($oRequest) {
                $dateInit = ($oRequest->dateInit != null) ? Carbon::parse($oRequest->dateInit)->format('Y-m-d') . ' 00:00:00' : null;
                $dateEnd = ($oRequest->dateEnd != null) ? Carbon::parse($oRequest->dateEnd)->format('Y-m-d') . ' 23:59:59' : Carbon::now()->format('Y-m-d') . ' 23:59:59';
                if ($dateInit !== null) {
                    return $q
                        ->wherebetween('checkin', [$dateInit, $dateEnd]);
                }
            })
            ->orderBy('checkin')
            ->get();
        return $this->cReportRepo->getHistoricAssociate($associate, $checks);
    }

    public function getReportAssociates(Request $oRequest)
    {
        $this->authorize('Reportes.Asistencias');
        $checks = $this->mCheckin
            ->where(function ($q) use ($oRequest) {
                $dateInit = ($oRequest->dateInit != null) ? Carbon::parse($oRequest->dateInit)->format('Y-m-d') . ' 00:00:00' : null;
                $dateEnd = ($oRequest->dateEnd != null) ? Carbon::parse($oRequest->dateEnd)->format('Y-m-d') . ' 23:59:59' : Carbon::now()->format('Y-m-d') . ' 23:59:59';
                if ($dateInit !== null) {
                    return $q
                        ->wherebetween('checkin.checkin', [$dateInit, $dateEnd]);
                }
            })
            ->join('associates', 'associates.id', '=', 'checkin.associate_id')
            ->join('shifts', 'shifts.id', '=', 'associates.shift_id')
            ->where(function ($q) {
                if (Auth::user()->area_id != null) {
                    return $q->where('associates.area_id', Auth::user()->area_id);
                }
            })
            ->orderBy('checkin.created_at', 'asc')
            ->select('checkin.*', 'associates.name', 'associates.employee_number', 'shifts.name as shift')
            ->orderBy('checkin.checkin')
            ->get();
        return $this->cReportRepo->getHistoricGeneral($checks);
    }

    public function getReportExtraHours()
    {
        $this->authorize('Productividad.Horas Extra');
        return Inertia::render('Reports/ExtraHours/Index');
    }

    public function getDataExtraHours(Request $oRequest)
    {
        $this->authorize('Productividad.Horas Extra');
        $associates = Associate::join('areas', 'areas.id', '=', 'associates.area_id')
            ->join('subareas', 'subareas.id', '=', 'associates.subarea_id')
            ->join('shifts', 'shifts.id', '=', 'associates.shift_id')
            ->where(function ($q) {
                if (Auth::user()->area_id != null) {
                    return $q
                        ->where('associates.area_id', Auth::user()->area_id);
                }
            })
            ->with(['checkin' => function ($q) use ($oRequest) {
                $startDay = ($oRequest->dateInit != null) ? Carbon::parse($oRequest->dateInit)->format('Y-m-d') . ' 00:00:00' : Carbon::now()->subDays(7)->format('Y-m-d 00:00:00');
                $endDay = ($oRequest->dateEnd != null) ? Carbon::parse($oRequest->dateEnd)->format('Y-m-d') . ' 23:59:59' : Carbon::now()->format('Y-m-d') . ' 23:59:59';
                if ($startDay !== null) {
                    $q->whereBetween('checkin', [$startDay, $endDay]);
                }
                $q->join('associates', 'associates.id', '=', 'checkin.associate_id');
                $q->select(DB::raw('TIMESTAMPDIFF(MINUTE, checkin.checkin, checkin.checkout) / 60 as register_hours'), 'checkin.*', 'associates.name', 'associates.employee_number');
                $q->orderBy('checkin.checkin', 'asc');
            }])
            ->select(
                'associates.*',
                'subareas.name as associate_type',
                DB::raw('TIMESTAMPDIFF(HOUR, shifts.checkin, shifts.checkout) as assign_hours'),
                'shifts.checkin as in'
            )->get();

        foreach ($associates as $k => $associate) {
            $unionized = (bool) $associate->unionized;
            $associates[$k]->assign_hours = ($associate->assign_hours < 0) ? $associate->assign_hours + 24 : $associate->assign_hours;
            foreach ($associate->checkin as $key => $register) {
                $tolerance = Carbon::parse($register->checkin)->format('Y-m-d') . ' ' . Carbon::parse($associate->in)->addMinutes(Shift::TOLERANCE)->format('H:i:s');
                $startTime = Carbon::parse($tolerance);
                $finishTime = Carbon::parse($register->checkin)->format('Y-m-d') . ' ' . Carbon::parse($register->checkin)->format('H:i:s');
                $finishTime = Carbon::parse($finishTime);
                $diff = 0;
                if ($finishTime < $startTime) {
                    $totalDuration = $finishTime->diffInSeconds($startTime);
                    $diff = $totalDuration / 3600;
                    $register->register_hours = $register->register_hours + (float)number_format($diff, 2);
                }
                if ($unionized === false || $associate->area_id === Area::SORTER) {
                    $hours =  floor($register->register_hours - $associate->assign_hours);
                } else {
                    $decimal = number_format($register->register_hours - $associate->assign_hours, 1);
                    $hours = ($decimal < 0) ? 0 : (floor((($decimal * 100)) / 50) * 50) / 100;
                }
                $associate->checkin[$key]['extra'] = $hours;
                $associate->checkin[$key]['tolerance'] = $tolerance;
                $associate->checkin[$key]['diff'] = (float)number_format($diff, 2);
            }
        }
        return response()->json($associates);
    }

    public function getDataPickingBonus(Request $oRequest)
    {
        // $this->authorize('Productividad.Picking');
        $bonus = PickingBonus::join('associates', 'associates.id', '=', 'picking_bonus.associate_id')
            ->join('areas', 'areas.id', '=', 'associates.area_id')
            ->where(function ($q) use ($oRequest) {
                if (Auth::user()->area_id != null && Auth::user()->id != 1) {
                    $q->where('associates.area_id', Auth::user()->area_id);
                }
                if ($oRequest->dateInit) {
                    $q->where('bonus_date', '>=', $oRequest->dateInit);
                }
                if ($oRequest->dateEnd) {
                    $q->where('bonus_date', '<=', $oRequest->dateEnd);
                }
                return $q;
            })
            ->select(
                'picking_bonus.id',
                'associates.employee_number',
                'associates.name',
                'areas.name as area',
                'picking_bonus.bonus_date',
                'picking_bonus.bonus_amount',
                'picking_bonus.boxes_shift'
            )
            ->orderBy('bonus_date')
            ->get();

        return ApiResponses::okObject($bonus);
    }


    public function getExcelExtraHours(Request $oRequest)
    {
        $this->authorize('Productividad.Horas Extra');
        $startDay = ($oRequest->dateInit != null) ? Carbon::parse($oRequest->dateInit)->format('Y-m-d') . ' 00:00:00' : Carbon::now()->subDays(7)->format('Y-m-d') . ' 00:00:00';
        $endDay = ($oRequest->dateEnd != null) ? Carbon::parse($oRequest->dateEnd)->format('Y-m-d') . ' 23:59:59' : Carbon::now()->subDays(1)->format('Y-m-d') . ' 23:59:59';

        return $this->cReportRepo->runReportHours($startDay, $endDay, Auth::user()->area_id, false);
    }

    public function getExcelPickingBonus(Request $oRequest)
    {
        // $this->authorize('Productividad.Picking');
        $startDay = ($oRequest->dateInit != null) ? Carbon::parse($oRequest->dateInit)->format('Y-m-d') . ' 00:00:00' : Carbon::now()->subDays(7)->format('Y-m-d') . ' 00:00:00';
        $endDay = ($oRequest->dateEnd != null) ? Carbon::parse($oRequest->dateEnd)->format('Y-m-d') . ' 23:59:59' : Carbon::now()->subDays(1)->format('Y-m-d') . ' 23:59:59';

        return $this->cReportRepo->runReportPickingBonus($startDay, $endDay);
    }

    /**
     * @param $skuArray
     * @return \Illuminate\Http\Response
     */
    public function loadPickingProductivity(Request $request)
    {
        // $this->authorize('Productividad.Picking');
        try {
            $validate = $this->cReportRepo->loadPickingProductivity($request->data);
            return response()->json($validate);
        } catch (\Exception $e) {
            return response()->json($e);
        }
    }

    public function getPicking()
    {
        $this->authorize('Productividad.Picking');
        return Inertia::render('Reports/Picking/Index');
    }
}
