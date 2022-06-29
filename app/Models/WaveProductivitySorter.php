<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaveProductivitySorter extends Model
{
    use HasFactory;

    protected $table = 'waves_productivity_sorter';
    protected $fillable = ['wave', 'stops'];
}
