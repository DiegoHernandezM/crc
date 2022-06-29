<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssociateSubarea extends Model
{
    use HasFactory;

    protected $table = 'associate_subarea';
    protected $fillable = ['created_at' ,'associate_id', 'subarea_id', 'from', 'to'];

    public function associate()
    {
        return $this->belongsTo(Associate::class)->withDefault();
    }

    public function subarea()
    {
        return $this->belongsTo(Subarea::class)->withDefault();
    }
}
