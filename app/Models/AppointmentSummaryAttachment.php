<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentSummaryAttachment extends Model
{
    protected $guarded = ['id'];

    public function getFileAttribute($value){
        return asset("storage/appointments/summary/$this->appointment_id/$value");
    }
}
