<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shift extends Model
{
    use HasFactory, SoftDeletes;

    const TOLERANCE = 15;

    protected $fillable = ['name', 'diff_hours', 'area_id', 'extra_hours', 'shifts'];

    protected $casts = [
        'shifts' => 'array'
    ];

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function getDiffHoursAttribute()
    {
        return $this->attributes['checkout'] - $this->attributes['checkin'];
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where('name', 'like', '%'.$search.'%');
        });
    }

    public function getShiftsAttribute()
    {
        $shifts = json_decode($this->attributes['shifts']);
        foreach ($shifts as $k => $shift) {
            $checkin = Carbon::parse($shift->checkin);
            $checkout = Carbon::parse($shift->checkout);
            $shift->assign = $checkout->diffInHours($checkin);
            $shifts[$k] = $shift;
        }
        return $shifts;
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = strtoupper($value);
    }
}
