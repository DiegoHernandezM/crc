<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Area extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name'];

    const PICKING   = 1;
    const SORTER    = 2;
    const AUDITORY    = 3;

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = strtoupper($value);
    }

    public function getNameAttribute($value)
    {
        return $this->attributes['name'] = strtoupper($value);
    }

    public function subareas()
    {
        return $this->hasMany('App\Models\Subarea', 'area_id');
    }

    public function associates()
    {
        return $this->hasMany('App\Models\Associate', 'area_id');
    }

    public function shifts()
    {
        return $this->hasMany('App\Models\Shift', 'area_id');
    }
}
