<?php

namespace App\Repositories;

use App\Models\Area;

class AreaRepository
{
    protected $mArea;

    public function __construct()
    {
        $this->mArea = new Area();
    }

    public function getAllAreas()
    {
        if (auth()->user()->isSuperUser()) {
            return $this->mArea->all();
        } else {
            return $this->mArea->where('id', auth()->user()->area_id)->get();
        }
    }

    public function createArea($request)
    {
        return $this->mArea->create($request->all());
    }

    public function getArea($id)
    {
        return $this->mArea->withTrashed()->find($id);
    }

    public function updateArea($id, $request)
    {
        $area = $this->getArea($id);
        if ($area) {
            $area->name = $request->name;
            $area->save();
            return $area;
        }
    }

    public function destroyArea($id)
    {
        return $this->mArea->destroy($id);
    }

    public function restoreArea($id)
    {
        return $this->mArea->withTrashed()->find($id)->restore();
    }
}
