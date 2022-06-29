<?php

namespace App\Repositories;

use App\Models\Associate;
use App\Models\Checkin;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AttendanceRepository
{
    protected $mAssociate;
    protected $mCheckin;
    protected $mShift;

    public function __construct()
    {
        $this->mAssociate = new Associate();
        $this->mCheckin = new Checkin();
        $this->mShift = new Shift();
    }

    public function getAll($request)
    {
        $associates = $this->mAssociate
            ->where(function ($q) {
                if (Auth::user()->area_id) {
                    return $q->where('area_id', Auth::user()->area_id);
                }
            })
            ->with(['checkin' => function($q) use ($request) {
                $init = $request->init ? Carbon::parse($request->init)->format('Y-m-d'). ' 00:00:00' : null;
                $end = $request->end ? Carbon::parse($request->end)->format('Y-m-d'). ' 23:59:59' : null;
                if ($init !== null) {
                    $q->whereBetween('checkin.checkin', [$init, $end]);
                }
                return $q;
            }])
            ->with('shift')
            ->get();
        foreach ($associates as $associate) {
            if (count($associate['checkin']) > 0) {
                foreach ($associate['checkin'] as $key => $checkin) {
                    if ($checkin->checkin !== null && $checkin->checkout !== null) {
                        foreach ($associate->shift->shifts as $shift) {
                            if ($shift->day === Carbon::parse($checkin->checkin)->dayOfWeek) {
                                $checkin->extra = $this->getHours($associate->unionized, $checkin->hours, $shift->assign);
                                $checkin->register =  (float)$checkin->hours;
                                $checkin->assign = $shift->assign;
                                $associate['checkin'][$key] = $checkin;
                            }
                        }
                    }
                }
            }
        }
        return $associates;
    }

    public function getHistoric($request)
    {
        $checks = $this->mCheckin
            ->where(function ($q) use ($request) {
                $init = ($request->init != null) ? Carbon::parse($request->init)->format('Y-m-d').' 00:00:00' : null;
                $end = ($request->end != null) ? Carbon::parse($request->end)->format('Y-m-d').' 23:59:59' : Carbon::now()->format('Y-m-d').' 23:59:59';
                if ($init !== null) {
                    return $q
                        ->wherebetween('checkin.checkin', [$init, $end]);
                }
            })
            ->with(['associate' => function($q) {
                if (Auth::user()->area_id != null) {
                    $q->where('associates.area_id', Auth::user()->area_id);
                }
                $q->with('shift');
                return $q;
            }])
            ->orderBy('checkin.created_at', 'asc')
            ->get();

        return (new ReportRepository)->getHistoricGeneral($checks);
    }

    public function getHistoricAssociate($id, $request)
    {
        $associate = $this->mAssociate
            ->where('id', $id)
            ->with('area')
            ->with('subarea')
            ->with('shift')
            ->with(['checkin' => function($q) use ($request) {
                $init = ($request->init != null) ? Carbon::parse($request->init)->format('Y-m-d').' 00:00:00' : null;
                $end = ($request->end != null) ? Carbon::parse($request->end)->format('Y-m-d').' 23:59:59' : Carbon::now()->format('Y-m-d').' 23:59:59';
                if ($init !== null) {
                    return $q
                        ->wherebetween('checkin.checkin', [$init, $end])
                        ->orderBy('checkin.checkin');
                }
            }])
            ->get();

        return (new ReportRepository)->getHistoricAssociate($associate);
    }

    private function getHours($unionized, $hours, $assignHours)
    {
        if ((bool)$unionized === false) {
            $extra =  $hours - $assignHours < 0 ? round($hours - $assignHours): floor($hours - $assignHours);
        } else {
            $decimal = number_format($hours - $assignHours, 1);
            $extra = ($decimal < 0) ? round($decimal) : (floor((($decimal * 100)) / 50) * 50) / 100;
        }
        return $extra;
    }
}
