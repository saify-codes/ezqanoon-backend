<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];

    /**
     * Set or update the option with the given name and value.
     * If the option already exists, it will be overwritten.
     *
     * @param string $name
     * @param string $value
     */
    public static function set($name, $value, $global = null, $adminId = null, $firmId = null, $lawyerId = null, $teamId = null)
    {
        self::updateOrInsert(
            [
                'global'    => $global,
                'admin_id'  => $adminId,
                'firm_id'   => $firmId,
                'lawyer_id' => $lawyerId,
                'team_id'   => $teamId,
                'name'      => $name
            ],
            [
                'value' => $value
            ]
        );
    }

    /**
     * Unset (delete) the option with the given name.
     */
    public static function unset($name, $adminId, $firmId = null, $lawyerId = null, $teamId = null)
    {
        self::where([
            'name'      => $name,
            'admin_id'  => $adminId,
            'firm_id'   => $firmId,
            'lawyer_id' => $lawyerId,
            'team_id'   => $teamId,
        ])->delete();
    }

    /**
     * Get (retrieve) the option value with the given name.
     */
    public static function get($name, $global = null, $adminId = null, $firmId = null, $lawyerId = null, $teamId = null)
    {
        return self::where([
            'name'      => $name,
            'global'    => $global ?? 1,
            'admin_id'  => $adminId,
            'firm_id'   => $firmId,
            'lawyer_id' => $lawyerId,
            'team_id'   => $teamId,
        ])->value('value');
    }
}
