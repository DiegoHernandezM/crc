<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subarea extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'area_id'];

    public function setNameAttribute($value)
    {
        return $this->attributes['name'] = strtoupper($value);
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where('name', 'like', '%'.$search.'%');
        });
    }
}
