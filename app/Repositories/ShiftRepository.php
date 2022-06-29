<?php

namespace App\Repositories;

use App\Models\Shift;

class ShiftRepository
{
    protected $mShift;
    public function __construct()
    {
        $this->mShift = new Shift();
    }

    public function getAll($request)
    {
        if (auth()->user()->isSuperUser()) {
            return $this->mShift->with('area')->get();
        } else {
            return $this->mShift->where('area_id', auth()->user()->area_id)->with('area')->get();
        }
    }

    public function createShift($request)
    {
        return  $this->mShift->create($request->all());
    }

    public function getShift($id)
    {
        return $this->mShift->withTrashed()->find($id);
    }

    public function updateShift($id, $request)
    {
        $shift = $this->getShift($id);
        if ($shift) {
            $shift->name = $request->name;
            $shift->shifts = $request->shifts;
            $shift->area_id = $request->area_id;
            $shift->save();
            return $shift;
        }
    }

    public function destroyShift($id)
    {
        return $this->mShift->destroy($id);
    }

    public function restoreShift($id)
    {
        return $this->mShift->withTrashed()->find($id)->restore();
    }

    public function getByArea($area)
    {
        return $this->mShift->where('area_id', $area)->get();
    }
}
