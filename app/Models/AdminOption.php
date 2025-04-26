<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AdminOption extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];

    /**
     * Set or update the admin option with the given name and value.
     * If the option already exists, it will be overwritten.
     *
     * @param string $name
     * @param string $value
     */
    public static function set($name, $value)
    {
        self::updateOrInsert(
            ['name' => $name], 
            ['value' => $value] 
        );
    }

    /**
     * Unset (delete) the admin option with the given name.
     *
     * @param string $name
     */
    public static function unset($name)
    {
        self::where('name', $name)->delete();
    }
    
    /**
     * get (retrive) the admin option with the given name.
     *
     * @param string $name
     */
    public static function get($name)
    {
        return self::where('name', $name)->value('value');
    }
}
