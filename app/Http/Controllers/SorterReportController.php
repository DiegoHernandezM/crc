<?php

namespace App\Http\Controllers;

use App\Models\Associate;
use App\Models\BonusStaffManager;
use App\Models\Checkin;
use App\Models\ProductivitySorter;
use App\Models\SorterBonus;
use App\Models\WaveProductivitySorter;
use App\Repositories\ReportRepository;
use App\Repositories\SorterReportRepository;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Support\Facades\Request;
use Illuminate\Http\Request as RequestHttp;
use Illuminate\Support\Facades\Redis;
use DB;

class SorterReportController extends Controller
{
    protected $mAssociates;
    protected $mCheckin;
    protected $mWaveProdSorter;
    protected $mProductivitySorter;
    protected $mSorterBonus;
    protected $mBonusStaff;
    protected $cReportRepo;
    protected $cSorterReportRepo;

    public function __construct()
    {
        $this->mAssociates = new Associate();
        $this->mCheckin = new Checkin();
        $this->mWaveProdSorter = new WaveProductivitySorter;
        $this->mProductivitySorter = new ProductivitySorter();
        $this->mSorterBonus = new SorterBonus();
        $this->mBonusStaff = new BonusStaffManager();
        $this->cReportRepo = new ReportRepository();
        $this->cSorterReportRepo = new SorterReportRepository();
    }

    public function productivitySorter()
    {
        $this->authorize('Productividad.Sorter');
        return Inertia::render('Reports/Sorter/Index');
    }

    public function getDataBonus(RequestHttp $oRequest)
    {
        $this->authorize('Productividad.Sorter');
        $bonus = $this->mSorterBonus->join('associates', 'associates.id', '=', 'sorter_bonus.associate_id')
            ->join('areas', 'areas.id', '=', 'associates.area_id')
            ->where(function ($q) use ($oRequest) {
                if (Auth::user()->area_id != null) {
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
                'associates.employee_number',
                'associates.name',
                'associates.wamas_user',
                'areas.name as area',
                'sorter_bonus.bonus_date',
                'sorter_bonus.bonus_amount',
                'sorter_bonus.ppk_shift'
            )->get();

        return response()->json($bonus);
    }

    public function dataExcelWamas(RequestHttp $oRequest)
    {
        $this->authorize('Productividad.Sorter');
        try {
            $waves = [];
            $values = $this->cReportRepo->getDataFromExcel($oRequest->file);
            $aValues = [];

            foreach ($values as $key => $value) {
                if ($value['Número de ola'] != '--') {
                    $waves[] = [
                        'wave' => $value['Número de ola'],
                        'stops' => 0,
                    ];
                }
                if ($value['Usuario'] !== 'Agarcia') {
                    $aValues[$value['Usuario']][$value['Número de ola']][$key] = $value;
                }
            }
            foreach ($aValues as $key => $aValue) {
                if ($key != '--') {
                    $aValues[$key] = $this->calculateValues($aValue);
                } else {
                    unset($aValues[$key]);
                }
            }

            $waves = array_map("unserialize", array_unique(array_map("serialize", $waves)));
            $waves = array_values($waves);

            $aValues = array_map(function ($a) {
                return array_pop($a);
            }, $aValues);
            $aValues = array_values(array_filter($aValues));

            $associates = [];
            foreach ($aValues as $k => $aValue) {
                $associates[] = [
                    'pieces' => $aValue['pieces'] ?? null,
                    'ppk' => $aValue['ppk'] ?? null,
                    'wave' => $aValue['wave'] ?? null,
                    'date' => $aValue['date'] ?? null,
                    'active_time' => $aValue['active_time'] ?? null,
                    'sorter' => $aValue['sorter'] ?? null,
                    'inductions' => $aValue['inductions'] ?? null,
                    'associate_id' => $aValue['associate_id'] ?? null,
                    'total_time' => $aValue['total_time'] ?? null,
                    'stops' => $aValue['stops'] ?? null,
                    'user' => $aValue['user'] ?? null,
                    'name' => $aValue['name'] ?? null,
                    'first_induction' => $aValue['first_induction'] ?? null,
                    'last_induction'  => $aValue['last_induction'] ?? null
                ];
            }

            foreach ($associates as $k => $associate) {
                if ($associate['pieces'] === null) {
                    unset($associates[$k]);
                }
            }

            $response = [
                'waves' => $waves,
                'associates' => array_values(array_filter($associates))
            ];
            return response()->json($response);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function calculateValues($array)
    {
        $this->authorize('Productividad.Sorter');
        foreach ($array as $k => $value) {
            $pieces = array_reduce($value, function (&$res, $item) {
                return $res + (int)$item['Piezas'];
            }, 0);

            if (is_numeric($k) || $pieces > 0) {
                $start = Carbon::parse($value[array_key_first($value)]['Created']);
                $end = Carbon::parse($value[array_key_last($value)]['Created']);
                $diff = $start->diffInSeconds($end);
                $inductions = 0;

                foreach ($value as $key => $content) {
                    if ($content['Categoría'] === 'Induce artículo') {
                        $associate = Redis::get('wamasusers:'. $content['Usuario']);
                        if ($associate) {
                            $subarea = Redis::get('wamasusers:'. $content['Usuario'].':subarea');
                            if ($subarea === 'Induccion') {
                                $array[$k] = [
                                    'pieces' => $pieces,
                                    'ppk' => round($pieces/2),
                                    'wave' => $k,
                                    'date' => Carbon::parse($content['Created'])->format('Y-m-d'),
                                    'active_time' => (float)number_format($diff/3600, 2),
                                    'sorter' => $content['Area'],
                                    'inductions' => $inductions++,
                                    'associate_id' => $associate ?? null,
                                    'total_time' => 0,
                                    'stops' => 0,
                                    'user' => $content['Usuario'],
                                    'name' => $associate ?? null,
                                    'first_induction' => $end,
                                    'last_induction' => $start,
                                ];
                            }
                        } else {
                            $array[$k] = [];
                        }
                    } else {
                        unset($value[$key]);
                    }
                }
            } else {
                unset($array[$k]);
            }
        }
        foreach ($array as $key => $value) {
            if ($key != '--') {
                if (array_key_exists('associate_id', $value)) {
                    if ($value['associate_id'] != null) {
                        $wave = $this->mWaveProdSorter->updateOrCreate(
                            ['wave' =>$value['wave']],
                            ['stops' => DB::raw('stops') ?? 0],
                        );
                        $this->mProductivitySorter->upsert(
                            [
                                'pieces' => $value['pieces'],
                                'ppk' => $value['ppk'],
                                'wave' => $value['wave'],
                                'date' => $value['date'],
                                'active_time' => $value['active_time'],
                                'sorter' => $value['sorter'],
                                'inductions' => $value['inductions'],
                                'associate_id' => $value['associate_id'],
                                'total_time' => 0,
                                'stops' => 0,
                                'wave_id' => $wave->id ?? null,
                                'first_induction' => $value['first_induction'],
                                'last_induction' => $value['last_induction'],
                            ],
                            ['associate_id', 'wave'],
                            ['pieces', 'ppk', 'wave', 'date', 'active_time', 'sorter', 'inductions', 'associate_id',
                                'wave_id', 'first_induction', 'last_induction'],
                        );
                    }
                }
            }
        }
        return (array)$array;
    }

    public function subtractStopsSorter(RequestHttp $oRequest)
    {
        $this->authorize('Productividad.Sorter');
        try {
            if ($oRequest->type === 'waves') {
                $this->mWaveProdSorter->where('wave', '=', $oRequest->waves['wave'])->update([
                    'stops' => $oRequest->waves['stops']
                ]);
            } else {
                $this->mProductivitySorter->where('associate_id', '=', $oRequest->waves['associate_id'])->where('wave', '=', $oRequest->waves['wave'])
                    ->update([
                        'stops' => $oRequest->waves['stops']
                    ]);
            }
            return response()->json(['message' => 'ok']);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function calculateTimesSorter(RequestHttp $oRequest)
    {
        $this->authorize('Productividad.Sorter');
        if (count($oRequest->waves) > 0) {
            foreach ($oRequest->waves as $data) {
                $wave = $this->mWaveProdSorter->where('wave', $data['wave'])->first();
                $stopWave = (float)number_format($wave->stops/60, 2);
                $this->mProductivitySorter->where('wave_id', '=', $wave->id)
                    ->update([
                        'total_time' => DB::raw('active_time - ('.$stopWave.'+(stops/60))')
                    ]);
            }
        }
        return response()->json(['message' => 'ok']);
    }

    public function calculateBonusSorter(RequestHttp $oRequets)
    {
        $this->authorize('Productividad.Sorter');
        $today = Carbon::parse("last Wednesday");
        $startDay = ($oRequets->init != null) ? $oRequets->init : Carbon::parse($today)->subDays(7)->format('Y-m-d');
        $endDay = ($oRequets->end != null) ? $oRequets->end : $startDay;
        $bonus  = $this->cSorterReportRepo->calculateSorterBonus($startDay, $endDay);
        return response()->json(['message' => $bonus]);
    }

    public function getReportBonus(RequestHttp $oRequest)
    {
        $this->authorize('Productividad.Sorter');
        $startDay = ($oRequest->dateInit != null) ? Carbon::parse($oRequest->dateInit)->format('Y-m-d').' 00:00:00' : Carbon::now()->subDays(7)->format('Y-m-d'). ' 00:00:00';
        $endDay = ($oRequest->dateEnd != null) ? Carbon::parse($oRequest->dateEnd)->format('Y-m-d').' 23:59:59' : Carbon::now()->subDays(1)->format('Y-m-d').' 23:59:59';

        return $this->cSorterReportRepo->runReportBonus($startDay, $endDay);
    }

    public function calculateBonusStaffSorter(RequestHttp $oRequest)
    {
        $this->authorize('Productividad.Sorter');
        $yearWeek = Carbon::parse($oRequest->init)->year.''.Carbon::parse($oRequest->init)->startOfWeek()->weekOfYear;
        $this->cSorterReportRepo->processBonusStaff($yearWeek);
        return $this->cSorterReportRepo->runReportBonusStaff(Carbon::parse($oRequest->init)->format('Y-m-d'), Carbon::parse($oRequest->end)->format('Y-m-d'), false);
    }

    public function getReportStaffManagerBonus(RequestHttp $oRequest)
    {
        $this->authorize('Productividad.Sorter');
        return $this->cSorterReportRepo->runReportBonusStaff(Carbon::parse($oRequest->init)->format('Y-m-d'), Carbon::parse($oRequest->end)->format('Y-m-d'), false);
    }

    public function getPending()
    {
        $this->authorize('Productividad.Sorter');
        $productivity = $this->cSorterReportRepo->getPendingProductivity();

        return response()->json(['productivity' => $productivity]);
    }

    public function getBonusStaffSorter(RequestHttp $oRequest)
    {
        $this->authorize('Productividad.Sorter');
        $yearWeek = Carbon::parse($oRequest->init)->year.''.Carbon::parse($oRequest->init)->weekOfYear;

        $bonus = $this->mBonusStaff->join('associates', 'associates.id', '=', 'bonus_staff_managers.associate_id')
            ->join('areas', 'areas.id', '=', 'associates.area_id')
            ->join('subareas', 'subareas.id', '=', 'associates.subarea_id')
            ->where(function ($q) use ($yearWeek) {
                if (Auth::user()->area_id != null) {
                    $q->where('associates.area_id', Auth::user()->area_id);
                }
                $q->where('year_week', $yearWeek);
                return $q;
            })
            ->select(
                'associates.employee_number',
                'associates.name',
                'associates.wamas_user',
                'areas.name as area',
                'subareas.name as subarea',
                'bonus_staff_managers.year_week',
                'bonus_staff_managers.bonus_amount',
            )->get();

        return response()->json($bonus);
    }
}
