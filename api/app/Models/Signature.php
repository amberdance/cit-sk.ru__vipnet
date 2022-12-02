<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int id
 * @property string label
 */
class Signature extends Model
{
    protected $guarded = [];

    public function applications(): HasMany
    {
        return $this->hasMany(User::class, "id", "user_id");
    }
}
