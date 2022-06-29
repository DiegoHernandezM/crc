<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserReport extends Model
{
    use HasFactory;

    const PKHOURS = 'pk_extra_hours';
    const SRTPROD = 'srt_productivity';

    public $fillable = [
        'email',
        'name',
        'subscrited_to',
        'active',
    ];

}
