<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\Permission\Models\Permission;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        
        $permissions = $this->id === 1 ? Permission::all()->pluck('name') :
                        $this->getPermissionNames();
        $permissions = array_flip($permissions->toArray());
        
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'name' => $this->name,
            'email' => $this->email,
            'owner' => $this->owner,
            'photo' => $this->photo,
            'deleted_at' => $this->deleted_at,
            'account' => $this->whenLoaded('account'),
            'can' => $permissions,
            'area' => $this->area_id,
        ];
    }
}
