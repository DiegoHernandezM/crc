<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BonusStaffManager extends Model
{
    use HasFactory;
    protected $table = 'bonus_staff_managers';
    protected $fillable = [
        'id',
        'associate_id',
        'area_id',
        'subarea_id',
        'year_week',
        'bonus_amount'
    ];

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function subarea()
    {
        return $this->belongsTo(Area::class, 'subarea_id');
    }
}
