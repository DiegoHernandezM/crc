<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductivitySorter extends Model
{
    use HasFactory;

    protected $table = 'productivity_sorter';
    protected $fillable = ['associate_id', 'wave' , 'date', 'inductions', 'active_time', 'total_time', 'sorter',
        'pieces', 'ppk', 'bono', 'stops', 'wave_id', 'first_induction', 'last_induction'];

    public function associate()
    {
        return $this->belongsTo(Associate::class)->withDefault();
    }

    public function wave()
    {
        return $this->belongsTo(WaveProductivitySorter::class)->withDefault();
    }
}
