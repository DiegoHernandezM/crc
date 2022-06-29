<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\App;
use League\Glide\Server;

class Associate extends Model
{
    use softDeletes, HasFactory;

    protected $fillable = [
        'id',
        'name',
        'employee_number',
        'deleted_at',
        'area_id',
        'subarea_id',
        'shift_id',
        'associate_type_id',
        'entry_date',
        'status_id',
        'elegible',
        'picture',
        'user_saalma',
        'unionized',
        'count_areas',
        'subarea_since',
        'wamas_user',
    ];

    public function checkin()
    {
        return $this->hasMany('App\Models\Checkin', 'associate_id');
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function subarea()
    {
        return $this->belongsTo(Subarea::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function productivitySorter()
    {
        return $this->hasMany(ProductivitySorter::class);
    }

    public function bonusSorter()
    {
        return $this->hasMany(SorterBonus::class);
    }
}
