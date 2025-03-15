<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Lawyer extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }
    
    /**
     * profile
     *
     * @return void
     */
    public function profile()
    {
        return $this->only(['name', 'email', 'phone', 'avatar', 'verified_email']);
    }
    
    public function reviews()
    {
        return $this->hasMany(Rating::class, 'lawyer_id');
    }

    /**
     * Accessor: get avatar absolute url.
     *
     * @return string|null
     */
    public function getAvatarAttribute($value): ?string
    {
        return empty($value) ? asset("/storage/avatar.jpg") : asset("/storage/users/$this->id/avatars/$value");
    }

    /**
     * Accessor: Convert international format to local format (0312 345 6789).
     *
     * @return string|null
     */
    public function getPhoneAttribute($value): ?string
    {
        return $this->convertToLocalFormat($value);
    }

    /**
     * Mutator: Convert local format to international before storing (+923123456789).
     *
     * @param string|null $value
     */
    public function setPhoneAttribute($value)
    {
        $this->attributes['phone'] = $this->convertToInternationalFormat($value);
    }

    /**
     * Convert phone number from international format (+92 312 345 6789) to local (0312 345 6789).
     *
     * @param string|null $phone
     * @param string|null $countryCode
     * @return string|null
     */
    private function convertToLocalFormat(?string $phone, ?string $countryCode = '92'): ?string
    {
        if (!$phone) {
            return null;
        }

        // Remove all non-digit characters
        $cleanedPhone = preg_replace('/\D+/', '', $phone);

        // If it starts with the country code, strip it and add '0'
        if (strpos($cleanedPhone, $countryCode) === 0) {
            $localNumber = '0' . substr($cleanedPhone, strlen($countryCode));
            return $localNumber;
        }

        return $cleanedPhone;
    }

    /**
     * Convert phone number from local format (0312 345 6789) to international (+923123456789).
     *
     * @param string|null $phone
     * @param string|null $countryCode
     * @return string|null
     */
    private function convertToInternationalFormat(?string $phone, ?string $countryCode = '92'): ?string
    {
        if (!$phone) {
            return null;
        }

        // Remove all non-digit characters
        $cleanedPhone = preg_replace('/\D+/', '', $phone);

        // If it starts with 0, remove it before adding country code
        if (strpos($cleanedPhone, '0') === 0) {
            $cleanedPhone = substr($cleanedPhone, 1);
        }

        return "+" . $countryCode . $cleanedPhone;
    }
}
