<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PickingProductivity extends Model
{
    use HasFactory;

    protected $table = 'picking_productivity';


    protected $fillable = ['associate_id', 'wave_id', 'init_picking', 'end_picking', 'minutes', 'skus', 'boxes', 'saalmauser'];

    public function associate()
    {
        return $this->belongsTo(Associate::class)->withDefault();
    }
}
