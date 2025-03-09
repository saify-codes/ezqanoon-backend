<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LawyerNotification extends Model
{
    protected $guarded = ['id'];

    public function getCreatedAtAttribute($value)
    {
        return date('Y-m-d H:i:s', strtotime($value));
    }
}
