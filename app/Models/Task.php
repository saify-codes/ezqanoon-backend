<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $guarded = ['id'];

    public function member(){
        return $this->hasOne(Lawyer::class, 'id', 'assign_to');
    }

    public function getCreatedAtAttribute($createdAt){
        return Carbon::parse($createdAt)->format('Y-m-d h:i:s A');
    }
}
