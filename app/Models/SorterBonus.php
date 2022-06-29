<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SorterBonus extends Model
{
    use HasFactory;

    protected $table = 'sorter_bonus';
    protected $fillable = ['associate_id', 'bonus_date', 'ppk_shift', 'bonus_amount'];
}
