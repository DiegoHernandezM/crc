<?php

namespace App\Repositories;


use App\Models\Area;
use App\Models\Associate;
use App\Models\AssociateSubarea;
use App\Models\Subarea;

class AssociatesRepository
{
    protected $mAssociate;

    public function __construct()
    {
        $this->mAssociate = new Associate();
    }

    public function verifyEntry($pickingDay, $sorterDay)
    {
        try {
            $pickingAssociates = $this->mAssociate->where(function ($q) use ($pickingDay) {
                $q->where('elegible', 0);
                $q->where('area_id', Area::PICKING);
                $q->where('entry_date', '<', $pickingDay);
                return $q;
            })->update([
                'elegible' => 1
            ]);

            $sorterAssociates = $this->mAssociate->where(function ($q) use ($sorterDay) {
                $q->where('elegible', 0);
                $q->where('area_id', Area::SORTER);
                $q->where('entry_date', '<', $sorterDay);
                return $q;
            })->update([
                'elegible' => 1
            ]);

            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Actualiza registro de asociado/subarea y crea nuevo registro cuando haya cambio de subarea
     *
     * @param  App\Models\Associate $associate
     * @param  App\Models\Subarea $newSubarea
     * @return void
     */
    public function updateSubarea(Associate $associate, $newSubarea)
    {
        $associateSubarea = AssociateSubarea::where('associate_id', $associate->id)->whereNull('to')->latest('id')->first();
        if (!empty($associateSubarea)) {
            $associateSubarea->to = date(today());
            $associateSubarea->save();
        } else {
            $associateSubarea = new AssociateSubarea;
            $associateSubarea->associate_id = $associate->id;
            $associateSubarea->subarea_id = $associate->subarea_id;
            $associateSubarea->from = $associate->entry_date;
            $associateSubarea->to = date(today());
            $associateSubarea->save();
        }
        $newAssociateSubarea = new AssociateSubarea;
        $newAssociateSubarea->associate_id = $associate->id;
        $newAssociateSubarea->subarea_id = $newSubarea;
        $newAssociateSubarea->from = date(today());
        $newAssociateSubarea->to = null;
        $newAssociateSubarea->save();
    }
}
