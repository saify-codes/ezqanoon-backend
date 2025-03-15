<?php

namespace App\Models;

use App\Utils\PhoneFormatter;
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
    public function getPaymentMethodsAttribute($value)
    {
       
        $tags = json_decode($value) ?? [];
        return array_map(fn($tag)=> strtoupper(str_replace('_', ' ', $tag)), $tags);
    }

    /**
     * Accessor: Convert international format to local format (0312 345 6789).
     *
     * @return string|null
     */
    public function getPhoneAttribute($value): ?string
    {
        return PhoneFormatter::convertToLocalFormat($value);
    }

    /**
     * Mutator: Convert local format to international before storing (+923123456789).
     *
     * @param string|null $value
     */
    public function setPhoneAttribute($value)
    {
        $this->attributes['phone'] = PhoneFormatter::convertToInternationalFormat($value);
    }
}
