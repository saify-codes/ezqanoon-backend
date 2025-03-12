<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cases extends Model
{
    protected $guarded = ['id'];

    public function attachments(){
        return $this->hasMany(CaseAttachments::class, 'case_id', 'id');
    }
}
