<?php

namespace App\Models;

use App\Utils\Phone;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Team extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guarded  = ['id'];
    protected $hidden   = [
        'password',
        'remember_token',
    ];
    protected static array $owners = [
        'firm_id'   => Firm::class,
        'lawyer_id' => Lawyer::class,
        // Add new roles here, e.g.:
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'permissions'       => 'array',
        ];
    }

    public function hasPermission($permission)
    {
        return in_array($permission, $this->permissions ?? []);
    }

    public function owner()
    {
        foreach (self::$owners as $foreignKey => $model) {
            if ($this->$foreignKey) {
                return $this->belongsTo($model, $foreignKey);
            }
        }
    }

    public function getAvatarAttribute($value): ?string
    {
        return empty($value) ? asset("/assets/images/avatar.jpg") : asset("/storage/teams/$this->id/avatars/$value");
    }

    public function getCreatedAtAttribute($value)
    {
        return date('Y-m-d H:i:s', strtotime($value));
    }

    public function getPhoneAttribute($value): ?string
    {
        return Phone::convertToLocalFormat($value);

    }

    public function setPhoneAttribute($value)
    {
        $this->attributes['phone'] = Phone::convertToInternationalFormat($value, request()->country_code);
    }

    private function firm(){
        $this->hasOne(Firm::class, 'firm_id');
    }

    private function lawyer(){
        $this->hasOne(Lawyer::class, 'lawyer_id');
    }
}
