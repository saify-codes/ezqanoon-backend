<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceMilestone extends Model
{
    protected $guarded = ['id'];
    public $timestamps = false;

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
