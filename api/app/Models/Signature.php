<?php

namespace App\Models;

use App\Models\Application\Application;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int id
 * @property string label
 */
class Signature extends Model
{

    public $timestamps = false;
    protected $guarded = [];

    /**
     * @return HasMany
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, "signature_id", "id");
    }
}
