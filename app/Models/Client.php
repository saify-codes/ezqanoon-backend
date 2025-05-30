<?php

namespace App\Models;

use App\Utils\Phone;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'payment_methods' => 'array',
        'notes'           => 'array',
    ];


    function attachments()
    {
        return $this->hasMany(ClientAttachments::class, 'client_id', 'id');
    }

    /**
     * getCreatedAtAttribute
     *
     * @param  string $createdAt
     * @return Date
     */
    public function getCreatedAtAttribute($createdAt)
    {
        return Carbon::parse($createdAt)->format('Y-m-d h:i:s A');
    }

    /**
     * Accessor: Convert international format to local format (0312 345 6789).
     *
     * @return string|null
     */
    public function getPaymentMethods()
    {
       
        return array_map(fn($tag)=> strtoupper(str_replace('_', ' ', $tag)),  $this->payment_methods ?? []);
    }

    /**
     * Accessor: Convert international format to local format (0312 345 6789).
     *
     * @return string|null
     */
    public function getContactTimeAttribute($value): ?string
    {
        return Carbon::parse($value)->format('h:i A');
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

    /**
     * Mutator: Convert local format to international before storing (+923123456789).
     *
     * @param string|null $value
     */
    public function setPhoneAttribute($value)
    {
        $this->attributes['phone'] = Phone::convertToInternationalFormat($value, request()->country_code);
    }
}
