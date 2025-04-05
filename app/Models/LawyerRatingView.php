<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LawyerRatingView extends Model
{
    protected $table = 'lawyers_view';

    public $timestamps = false;

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
     * getAvailabilityFromAttribute
     *
     * @param  mixed $value
     * @return string
     */
    public function getAvailabilityFromAttribute($value): ?string
    {
        return date('h:m a');
    }
        
    /**
     * getAvailabilityFromAttribute
     *
     * @param  mixed $value
     * @return string
     */
    public function getAvailabilityToAttribute($value): ?string
    {
        return date('h:m a');
    }

}
