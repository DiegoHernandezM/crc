<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Board extends Model
{
    use HasFactory, softDeletes;

    protected $table = 'board';
    protected $fillable = [
        'id',
        'quantity',
        'bono',
        'area_id',
        'subarea_id',
    ];

    public function area()
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function subarea()
    {
        return $this->belongsTo(Subarea::class, 'subarea_id');
    }
}
