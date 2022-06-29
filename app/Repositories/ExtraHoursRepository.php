<?php

namespace App\Repositories;

use App\Models\Associate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExtraHoursRepository
{
    protected $mAssociate;

    public function __construct()
    {
        $this->mAssociate = new Associate();
    }

    public function getDataHours($request)
    {
        $associates = $this->mAssociate
            ->where(function ($q) {
                if (Auth::user()->area_id != null) {
                    return $q
                        ->where('associates.area_id', Auth::user()->area_id);
                }
            })
            ->with(['checkin' => function ($q) use ($request) {
                $startDay = ($request->init != null) ? Carbon::parse($request->init)->format('Y-m-d').' 00:00:00' : Carbon::now()->subDays(7)->format('Y-m-d 00:00:00');
                $endDay = ($request->end != null) ? Carbon::parse($request->end)->format('Y-m-d').' 23:59:59' : Carbon::now()->format('Y-m-d').' 23:59:59';
                if ($startDay !== null) {
                    $q->whereBetween('checkin', [$startDay, $endDay]);
                }
                $q->select(DB::raw('TIMESTAMPDIFF(MINUTE, checkin.checkin, checkin.checkout) / 60 as register_hours'), 'checkin.*');
                $q->orderBy('checkin.checkin');
            }])
            ->with('shift')
            ->get();

        foreach ($associates as $key => $associate) {

        }

        return $associates;
    }
}
