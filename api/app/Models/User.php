<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected $casts = [
        'created_at' => 'datetime:H:i:s Y-m-d',
        'updated_at' => 'datetime:H:i:s Y-m-d',
        'is_blocked' => 'boolean',
    ];
}