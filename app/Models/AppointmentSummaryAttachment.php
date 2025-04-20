<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentSummaryAttachment extends Model
{
    protected $guarded = ['id'];

    public function getFilePathAttribute($value){
        return asset("storage/$value");
    }
}
