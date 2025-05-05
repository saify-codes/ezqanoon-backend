<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
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
            'password'          => 'hashed',
            'permissions'       => 'array',
        ];
    }

    /**
     * Accessor: get avatar absolute url.
     *
     * @return string|null
     */
    public function getAvatarAttribute($value): ?string
    {
        return empty($value) ? asset("/storage/avatar.jpg") : asset("/storage/admins/$this->id/avatars/$value");
    }
    
}
