<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PickingBonus extends Model
{
    use HasFactory;

    protected $table = 'picking_bonus';

    protected $fillable = ['associate_id', 'bonus_date', 'boxes_shift', 'bonus_amount'];

    public function associate()
    {
        return $this->belongsTo(Associate::class)->withDefault();
    }
}
