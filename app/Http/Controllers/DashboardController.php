<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Associate;
use App\Models\PickingProductivity;
use App\Models\ProductivitySorter;
use App\Models\SorterBonus;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use DB;
use App\Models\Log as Logger;

class DashboardController extends Controller
{
    protected $mAssociate;
    protected $mSorterProductivity;
    protected $mPickingProductivity;

    public function __construct()
    {
        $this->mAssociate = new Associate();
        $this->mSorterProductivity = new ProductivitySorter();
        $this->mPickingProductivity= new PickingProductivity();
    }

    public function __invoke()
    {
        return Inertia::render('Dashboard/Index');
    }

    public function getSorterProdWeek()
    {
        try {
            $today = Carbon::parse("last Monday");
            $dataBonus = [];

            for ($i = 0; $i <= 7; $i++) {
                $data = $this->getProductivitySorterDay(Carbon::parse($today)->subDays($i)->format('Y-m-d'));
                $dataBonus[$i] = [
                    'day' => $startDay = Carbon::parse($today)->subDays($i)->format('Y-m-d'),
                    'ppk' => $data[0],
                    'pieces' => $data[1]
                ];
            }
            unset($dataBonus[0]);
            usort($dataBonus, function ($item1, $item2) {
                return $item2['day'] < $item1['day'];
            });
            return response()->json($dataBonus);
        } catch (\Exception $e) {
            $logData = [
                'message'       => $e->getMessage(),
                'loggable_id'   => Logger::LOG_SYSTEM,
                'loggable_type' => 'Sistem',
                'user_id'       => 1,
            ];
            $log = new Logger();
            $log->create($logData);
            return false;
        }
    }

    public function getPickingProdWeek()
    {
        try {
            $today = Carbon::parse("last Monday");
            $dataBonus = [];

            for ($i = 0; $i <= 7; $i++) {
                $dataBonus[$i] = [
                    'day' => $startDay = Carbon::parse($today)->subDays($i)->format('Y-m-d'),
                    'boxes' => $this->getProductivityPickingDay(Carbon::parse($today)->subDays($i)->format('Y-m-d'))
                ];
            }
            unset($dataBonus[0]);
            usort($dataBonus, function ($item1, $item2) {
                return $item2['day'] < $item1['day'];
            });
            return response()->json($dataBonus);
        } catch (\Exception $e) {
            $logData = [
                'message'       => $e->getMessage(),
                'loggable_id'   => Logger::LOG_SYSTEM,
                'loggable_type' => 'Sistem',
                'user_id'       => 1,
            ];
            $log = new Logger();
            $log->create($logData);
            return false;
        }
    }

    public function getAbsencesByDay()
    {
        try {
            $area = Auth::user()->area_id ?? null;
            $today = Carbon::parse("last Monday");

            $absences = [];
            for ($i = 0; $i <= 7; $i++) {
                $absences[$i] = [
                    'day' => $startDay = Carbon::parse($today)->subDays($i)->format('Y-m-d'),
                    'absences' => $this->getAbsencesDay(Carbon::parse($today)->subDays($i)->format('Y-m-d'), $area)
                ];
            }
            unset($absences[0]);
            $absences = array_values($absences);

            usort($absences, function ($item1, $item2) {
                return $item2['day'] < $item1['day'];
            });
            return response()->json($absences);
        } catch (\Exception $e) {
            $logData = [
                'message'       => $e->getMessage(),
                'loggable_id'   => Logger::LOG_SYSTEM,
                'loggable_type' => 'Sistem',
                'user_id'       => 1,
            ];
            $log = new Logger();
            $log->create($logData);
            return false;
        }
    }

    public function getBestExtraHours()
    {
        $this->authorize('Dashboard');
        try {
            $area = Auth::user()->area_id ?? null;
            $today = Carbon::parse("last Monday");
            $startDay = Carbon::parse($today)->subDays(7)->format('Y-m-d');
            $endDay = Carbon::parse($today)->subDays(1)->format('Y-m-d');

            $associates = $this->getAssociatesCheckinByDates($startDay, $endDay, $area);
            $associates = $this->getAssociatesCheckinByDates($startDay, $endDay, $area);
            foreach ($associates as $k => $associate) {
                if ($associate->assign_hours < 0) {
                    $associates[$k]->assign_hours = $associate->assign_hours + 24;
                }
            }

            foreach ($associates as $key => $associate) {
                if (count($associate->checkin) > 0) {
                    foreach ($associate->checkin as $k => $checkin) {
                        $hours = $this->calculateExtraHours($associate->assign_hours, $checkin, $associate->unionized);
                        $associates[$key]['total'] += $hours;
                        $associate->checkin[$k]['extra'] = $hours;
                    }
                } else {
                    $associates[$key]['total'] = 0;
                }
            }

            $associates = $associates->toArray();
            usort($associates, function ($item1, $item2) {
                return $item2['total'] <=> $item1['total'];
            });

            foreach ($associates as $k => $associate) {
                if ($associate['total'] <= 0) {
                    unset($associates[$k]);
                }
            }
            return response()->json($associates);
        } catch (\Exception $e) {
            $logData = [
                'message'       => $e->getMessage(),
                'loggable_id'   => Logger::LOG_SYSTEM,
                'loggable_type' => 'Sistem',
                'user_id'       => 1,
            ];
            $log = new Logger();
            $log->create($logData);
            return false;
        }
    }

    public function getAbsencesWeek()
    {
        $this->authorize('Dashboard');
        try {
            $area = Auth::user()->area_id ?? null;
            $today = Carbon::parse("last Monday");
            $startDay = Carbon::parse($today)->subDays(7)->format('Y-m-d');
            $endDay = Carbon::parse($today)->subDays(1)->format('Y-m-d');
            $countDays = Carbon::parse($startDay)->diffInDays(Carbon::parse($endDay));

            $associates = $this->getAssociatesCheckinByDates($startDay, $endDay, $area);

            foreach ($associates as $key => $associate) {
                $associates[$key]['absences'] = $countDays - count($associate->checkin);
            }

            $associatesAbsences = [];
            foreach ($associates as $associate) {
                if ($associate->absences != 0) {
                    $associatesAbsences[] = $associate;
                }
            }

            return response()->json($associatesAbsences);
        } catch (\Exception $e) {
            $logData = [
                'message'       => $e->getMessage(),
                'loggable_id'   => Logger::LOG_SYSTEM,
                'loggable_type' => 'Sistem',
                'user_id'       => 1,
            ];
            $log = new Logger();
            $log->create($logData);
            return false;
        }
    }

    private function getAssociatesCheckinByDates($startDay, $endDay, $byArea)
    {
        $this->authorize('Dashboard');
        $associates = $this->mAssociate->join('areas', 'areas.id', '=', 'associates.area_id')
            ->join('associate_types', 'associate_types.id', '=', 'associates.associate_type_id')
            ->join('subareas', 'subareas.id', '=', 'associates.subarea_id')
            ->join('shifts', 'shifts.id', '=', 'associates.shift_id')
            ->where(function ($q) use ($byArea) {
                if ($byArea !== null) {
                    return $q->where('associates.area_id', $byArea);
                }
            })
            ->with(['checkin' => function ($q) use ($startDay, $endDay) {
                $q->join('associates', 'associates.id', '=', 'checkin.associate_id');
                $q->whereBetween('checkin', [$startDay, $endDay]);
                $q->select(DB::raw('TIMESTAMPDIFF(MINUTE, checkin.checkin, checkin.checkout) / 60 as register_hours'), 'checkin.*', 'associates.name', 'associates.employee_number');
                $q->orderBy(DB::raw('TIMESTAMPDIFF(MINUTE, checkin.checkin, checkin.checkout) / 60'), 'desc');
            }])
            ->select(
                'associates.*',
                'associate_types.name as associate_type',
                'subareas.name as subarea',
                DB::raw('TIMESTAMPDIFF(HOUR, shifts.checkin, shifts.checkout) as assign_hours')
            )->get();

        return $associates;
    }

    private function calculateExtraHours($assign_hours, $checkin, $unionized)
    {
        $this->authorize('Dashboard');
        if ((boolean)$unionized === false) {
            $assign_hours = ($assign_hours < 0) ? $assign_hours + 24 : $assign_hours;
            $hours =  floor($checkin->register_hours - $assign_hours);
        } else {
            $assign_hours = ($assign_hours < 0) ? $assign_hours + 24 : $assign_hours;
            $decimal = number_format($checkin->register_hours - $assign_hours, 1);
            $hours = ($decimal < 0 ) ? 0 : (floor((($decimal * 100)) / 50) * 50) / 100;
        }

        return $hours;
    }

    private function getAbsencesDay($init, $area)
    {
        $associates = $this->mAssociate->where(function ($q) use ($area) {
            if ($area !== null) {
                return $q->where('associates.area_id', $area);
            }
        })->with(['checkin' => function ($q) use ($init) {
            $q->join('associates', 'associates.id', '=', 'checkin.associate_id');
            $q->where('checkin', 'like', '%'.$init.'%');
        }])->get();
        $absences = 0;
        foreach ($associates as $key => $associate) {
            if (count($associate->checkin) == 0) {
                $absences = $absences + 1;
            }
        }
        return $absences;
    }

    private function getProductivitySorterDay($day)
    {
        $productivity = $this->mSorterProductivity->where('date', 'like', '%'.$day.'%')->get();
        $ppk = 0;
        $pieces = 0;
        foreach ($productivity as $k => $prod) {
            $ppk += $prod->ppk;
            $pieces += $prod->pieces;
        }
        return [$ppk, $pieces];
    }

    private function getProductivityPickingDay($day)
    {
        $productivity = $this->mPickingProductivity->where('created_at', 'like', '%'.$day.'%')->get();
        $boxes = 0;
        foreach ($productivity as $k => $prod) {
            $boxes += $prod->boxes;
        }
        return $boxes;
    }
}
