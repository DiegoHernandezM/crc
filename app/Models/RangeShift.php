<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RangeShift extends Model
{
    use HasFactory;

    protected $table = 'range_dates_shifts';
    protected $fillable = ['area_id', 'day'];

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }
}
