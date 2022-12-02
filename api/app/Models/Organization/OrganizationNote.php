<?php

namespace App\Models\Organization;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int id
 * @property int organization_id
 * @property string created_at
 * @property string description
 */
class OrganizationNote extends Model
{

    protected $guarded = [];

    public function organization(): HasOne
    {
        return $this->hasOne(Organization::class, "note_id", "organization_id");
    }

}
