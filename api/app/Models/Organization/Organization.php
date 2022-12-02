<?php

namespace App\Models\Organization;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int id
 * @property int note_id
 * @property string created_at
 * @property string label
 * @property string city
 * @property string district
 * @property string tax_id
 * @property string government_id
 */
class Organization extends Model
{

    protected $guarded = [];

    public function notes(): HasMany
    {
        return $this->hasMany(OrganizationNote::class, "id", "note_id");
    }

}
