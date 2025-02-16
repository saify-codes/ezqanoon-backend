<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $guarded = ['id'];

    public function attachments(){
        return $this->hasMany(Attachment::class);
    }

    public function lawyer(){
        return $this->hasOne(Lawyer::class, 'id');
    }
}
