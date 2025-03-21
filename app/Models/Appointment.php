<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Appointment extends Model
{
    protected $guarded = ['id'];

    public function attachments(){
        return $this->hasMany(Attachment::class);
    }

    public function lawyer(){
        return $this->hasOne(Lawyer::class, 'id', 'lawyer_id');
    }
    
    public function user(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function getCreatedAtAttribute($createdAt)
    {
        return Carbon::parse($createdAt)->format('Y-m-d h:i:s A');
    }

}
