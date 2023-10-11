<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // Specify the database connection to be used for this model
    protected $connection = 'smAppTemplate';

    // Constants representing user roles
    const ROLE_ADMIN = 1;
    const ROLE_EDITOR = 2;
    const ROLE_AUDIT = 3;
    const ROLE_USER = 4;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'cover',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the human-readable name of the user's role.
     *
     * This function takes the role identifier as a parameter and returns
     * a human-readable string that represents the user's role in the application.
     * If the role identifier does not match any predefined roles, it returns
     * a default 'Função Desconhecida' string.
     *
     * @param int $role The role identifier.
     * @return string The human-readable name of the role.
     */
    public function getRoleName($role)
    {
        switch ($role) {
            case self::ROLE_ADMIN:
                return 'Administração';
            case self::ROLE_EDITOR:
                return 'Gerência';
            case self::ROLE_AUDIT:
                return 'Auditoria';
            case self::ROLE_USER:
                return 'Operacional';
            default:
                return 'Função Desconhecida';
        }
    }
}
