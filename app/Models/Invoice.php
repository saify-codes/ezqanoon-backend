<?php

namespace App\Models;

use App\Utils\Phone;
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

    /**
     * Accessor: Convert international format to local format (0312 345 6789).
     *
     * @return string|null
     */
    public function getPhoneAttribute($value): ?string
    {
        return Phone::convertToLocalFormat($value);
    }

    public function setPhoneAttribute($value)
    {
        $this->attributes['phone'] = Phone::convertToInternationalFormat($value, request()->country_code);
    }
}
