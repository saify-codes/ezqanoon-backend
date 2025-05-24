<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $guarded = ['id'];

    public function getCreatedAtAttribute($createdAt)
    {
        return Carbon::parse($createdAt)->format('Y-m-d h:i:s A');
    }
}
