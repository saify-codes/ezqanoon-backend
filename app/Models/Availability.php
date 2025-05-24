<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Availability extends Model
{
    public    $timestamps = false;
    protected $guarded = ['id'];

    public function firm()
    {
        return $this->belongsTo(Firm::class, 'firm_id');
    }

    public function lawyer()
    {
        return $this->belongsTo(Lawyer::class, 'lawyer_id');
    }

    public function getTimeAttribute($time){
        return Carbon::parse($time)->format('H:i');
    }
}
