<?php

namespace App\Models;

use App\Utils\Phone;
use Carbon\Carbon;
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
            'specialization'    => 'array',
        ];
    }
    
    public function availabilities()
    {
        return $this->hasMany(Availability::class, 'lawyer_id');
    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class, 'id', 'subscription_id');
    }

    public function team()
    {
        return $this->hasMany(Team::class, 'lawyer_id');
    }

    public function clients(){
        return $this->hasMany(Client::class, 'lawyer_id');
    }

    public function appointments(){
        return $this->hasMany(Appointment::class, 'lawyer_id');
    }
    
    public function cases(){
        return $this->hasMany(Cases::class, 'lawyer_id');
    }
    
    public function settings(){
        $settings = Option::get('settings', lawyerId:$this->id);
        return json_decode($settings);
    }

    public function getLicenceFrontAttribute($value): ?string
    {
        return empty($value) ? null : asset("/storage/lawyers/$this->id/licences/$value");
    }

    public function getLicenceBackAttribute($value): ?string
    {
        return empty($value) ? null : asset("/storage/lawyers/$this->id/licences/$value");
    }

    public function getSelfieAttribute($value): ?string
    {
        return empty($value) ? null : asset("/storage/lawyers/$this->id/selfies/$value");
    }

    public function getAvatarAttribute($value): ?string
    {
        return empty($value) ? asset("/assets/images/avatar.jpg") : asset("/storage/lawyers/$this->id/avatars/$value");
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

    /**
     * Accessor: Convert international format to local format (0312 345 6789).
     *
     * @return string|null
    */
    
     public function getCreatedAtAttribute($value): ?string
    {
        return Carbon::parse($value)->format('Y-m-d h:i:s A');
    }
           
    /**
     * getAvailabilityFromAttribute
     *
     * @param  mixed $value
     * @return string
     */
    public function getAvailabilityFromAttribute($value): ?string
    {
        return Carbon::parse($value)->format('h:i');
    }
           
    /**
     * getAvailabilityFromAttribute
     *
     * @param  mixed $value
     * @return string
     */
    public function getAvailabilityToAttribute($value): ?string
    {
        return Carbon::parse($value)->format('h:i');
    }
}
