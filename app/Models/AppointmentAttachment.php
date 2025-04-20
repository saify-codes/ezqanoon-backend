<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentAttachment extends Model
{
    protected $guarded = ['id'];

    public function getFileAttribute($value){
        return asset("storage/appointments/$this->appointment_id/$value");
    }
}
