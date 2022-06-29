<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    const LOG_SYSTEM    = 1;
    const LOG_MAIL      = 2;

    const TYPE_LOG = array(
        1 => 'LogSystem',
        2 => 'LogMail',
    );


    public $fillable = [
        'id',
        'message',
        'loggable_id',
        'loggable_type',
        'user_id',
    ];

    /**
     * Get the owning loggable model.
     */
    public function loggable()
    {
        return $this->morphTo();
    }
}
