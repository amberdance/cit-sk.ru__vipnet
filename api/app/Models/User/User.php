<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int id
 * @property string created_at
 * @property string updated_at
 * @property string login
 * @property string password
 * @property string first_name
 * @property string last_name
 * @property string patronymic
 * @property string phone
 * @property string email
 * @property boolean is_active
 */
class User extends Model
{
    use HasRoles;

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $hidden = [
        "password",
        "login",
        "created_at",
        "updated_at",
        "is_active",
    ];

    protected $fillable = [];
}
