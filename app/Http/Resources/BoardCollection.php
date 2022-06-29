<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class BoardCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->map->only(
            'id',
            'quantity',
            'bono',
            'area_id',
            'subarea_id',
            'area',
            'subarea'
        );
    }
}
