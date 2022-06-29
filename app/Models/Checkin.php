<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checkin extends Model
{
    use HasFactory;

    protected $table = 'checkin';
    protected $fillable = ['created_at' ,'associate_id', 'checkout', 'status', 'user_id', 'comments','checkin'];

    public function setCommentsAttribute($value)
    {
        $this->attributes['comments'] = strtoupper($value);
    }

    public function associate()
    {
        return $this->belongsTo(Associate::class)->withDefault();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getHoursAttribute()
    {
        $checkin = Carbon::parse($this->attributes['checkin']);
        $checkout = Carbon::parse($this->attributes['checkout']);
        return number_format((float)$checkout->diffInMinutes($checkin) / 60, 2, '.', '');
    }

}
