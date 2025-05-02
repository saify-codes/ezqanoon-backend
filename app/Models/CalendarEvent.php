<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CalendarEvent extends Model
{
    protected $guarded = ['id'];

    /**
     * getCreatedAtAttribute
     *
     * @param  string $createdAt
     * @return Date
     */
    public function getCreatedAtAttribute($createdAt)
    {
        return Carbon::parse($createdAt)->format('Y-m-d h:i:s A');
    }
}
