<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Cases extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'deadlines' => 'array',
    ];

    public function attachments(){
        return $this->hasMany(CaseAttachments::class, 'case_id', 'id');
    }

    public function getCreatedAtAttribute($createdAt){
        return Carbon::parse($createdAt)->format('Y-m-d h:m:i A');
    }
}
