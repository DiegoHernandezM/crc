<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class AssociateCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map->only(
            'id',
            'name',
            'employee_number',
            'area',
            'shift',
            'deleted_at',
            'area_id',
            'shift_id',
            'associate_type_id',
            'entry_date',
            'status_id',
            'elegible',
            'picture',
            'user_saalma',
            'unionized',
            'count_areas',
            'subarea_since',
            'wamas_user',
        );
    }
}