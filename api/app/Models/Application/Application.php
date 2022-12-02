<?php

namespace App\Models\Application;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int id
 * @property int organization_id
 * @property int signature_id
 * @property int person_count
 * @property string created_at
 * @property string updated_at
 * @property string reception_date
 * @property string note
 * @property boolean is_active
 */
class Application extends Model
{

    protected $guarded = [];

    public function notes(): HasOne
    {
        return $this->hasOne(Organization::class, "id", "organization_id");
    }

}
