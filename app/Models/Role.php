<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string name
 */
class Role extends Model
{
    public const ADMINISTRATOR_ROLE = 'administrator';
    public const USER_ROLE = 'user';
}
