<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentNote extends Model
{
    protected $guarded = ['id'];

    public function appointment(){
        $this->belongsTo(Appointment::class);
    }
}
