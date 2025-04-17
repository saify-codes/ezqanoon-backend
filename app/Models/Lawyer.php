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
            'permissions'       => 'array',
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

    public function subscription()
    {
        if($this->role == 'USER'){
            return Lawyer::find($this->lawyer_id)->hasOne(Subscription::class, 'id', 'subscription_id');
        }
        
        return $this->hasOne(Subscription::class, 'id', 'subscription_id');
    }

    public function hasPermission($permission)
    {
        switch ($this->role) {
            case 'USER':
                return in_array($permission, $this->permissions ?? []);
            case 'ADMIN':
                return true;
        }
    }

    public function team()
    {
        // If current user is USER type, get team members from their admin
        if ($this->role === 'USER') {
            return self::find($this->lawyer_id)
            ->hasMany(self::class, 'lawyer_id')
            ->where('role', 'USER')
            ->where('id', '!=', $this->id); // Exclude self from the list
        } 

        // If ADMIN, return all their users
        return $this->hasMany(self::class, 'lawyer_id')->where('role', 'USER');
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
        return Phone::convertToLocalFormat($value);

    }

    /**
     * Mutator: Convert local format to international before storing (+923123456789).
     *
     * @param string|null $value
     */
    public function setPhoneAttribute($value)
    {
        $this->attributes['phone'] = Phone::convertToInternationalFormat($value);
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
}
