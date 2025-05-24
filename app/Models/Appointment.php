<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Appointment extends Model
{
    protected $guarded = ['id'];

    public function attachments(){
        return $this->hasMany(AppointmentAttachment::class);
    }
    
    public function summaryAttachments(){
        return $this->hasMany(AppointmentSummaryAttachment::class);
    }

    public function firm(){
        return $this->hasOne(Firm::class, 'id', 'firm_id');
    }
    
    public function lawyer(){
        return $this->hasOne(Lawyer::class, 'id', 'lawyer_id');
    }
    
    public function user(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function notes(){
        $this->hasMany(AppointmentNote::class);
    }

    public function getCreatedAtAttribute($createdAt)
    {
        return Carbon::parse($createdAt)->format('Y-m-d h:i:s A');
    }

}
