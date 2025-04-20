<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $guarded = ['id'];
    protected $casts   = [
        'receipt' => 'array',
    ];

    public function milestone(){
        return $this->hasMany(InvoiceMilestone::class);
    }

    public function getCreatedAtAttribute($createdAt){
        return Carbon::parse($createdAt)->format('Y-m-d h:i:s A');
    }
}
