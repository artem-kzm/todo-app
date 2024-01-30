<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @property int id
 * @property string email
 * @property string password
 * @property string role
 */
class User extends Authenticatable
{
    use HasFactory;

    /** @var string[] */
    protected $fillable = [
        'email',
        'password'
    ];

    /** @var string[] */
    protected $hidden = [
        'password'
    ];

    public function isAdministrator(): bool
    {
        return $this->role === Role::ADMINISTRATOR_ROLE;
    }
}
