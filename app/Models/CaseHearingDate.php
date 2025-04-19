<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaseHearingDate extends Model
{
    protected $guarded = ['id'];
    public $timestamps = false;

    public function caseRelation()
    {
        return $this->belongsTo(Cases::class, 'case_id', 'id');
    }
}
