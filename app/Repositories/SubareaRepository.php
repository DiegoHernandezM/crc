<?php

namespace App\Repositories;

use App\Models\Subarea;

class SubareaRepository
{
    protected $mSubarea;
    public function __construct()
    {
        $this->mSubarea = new Subarea();
    }

    public function getAll()
    {
        if (auth()->user()->isSuperUser()) {
            return $this->mSubarea->with('area')->get();
        } else {
            return $this->mSubarea->where('area_id', auth()->user()->area_id)->with('area')->get();
        }
    }

    public function createSubarea($request)
    {
        return $this->mSubarea->create($request->all());
    }

    public function getSubarea($id)
    {
        return $this->mSubarea->find($id);
    }

    public function updateSubarea($id, $request)
    {
        $subarea = $this->getSubarea($id);
        if ($subarea) {
            $subarea->name = $request->name;
            $subarea->area_id = $request->areaId;
            $subarea->save();
            return $subarea;
        }
    }

    public function destroySubarea($id)
    {
        return $this->mSubarea->destroy($id);
    }

    public function restoreSubarea($id)
    {
        return $this->mSubarea->find($id)->restore();
    }

    public function getByArea($area)
    {
        return $this->mSubarea->where(function ($q) use ($area) {
            return $area ? $q->where('area_id', $area) : $q;
        })->get();
    }
}
