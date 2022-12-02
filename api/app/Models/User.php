<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $fillable = [];
}
