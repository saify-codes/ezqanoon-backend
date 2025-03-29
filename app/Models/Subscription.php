<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'features' => 'array',
    ];

    public function lawyer()
    {
        return $this->belongsTo(Lawyer::class);
    }   

    public function history()
    {
        return $this->hasMany(SubscriptionHistory::class);
    }
}
