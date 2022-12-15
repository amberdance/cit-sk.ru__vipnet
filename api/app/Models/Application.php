<?php

namespace App\Models;

use App\Models\Organization;
use App\Models\Signature;
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
    protected $casts   = [
        "reception_date" => "datetime:Y-m-d H:i:s",
        "is_active"      => "boolean",
    ];

    /**
     * @return HasOne
     */
    public function organization(): HasOne
    {
        return $this->hasOne(Organization::class, "id", "organization_id");
    }

    /**
     * @return HasOne
     */
    public function signature(): HasOne
    {
        return $this->hasOne(Signature::class, "id", "signature_id");
    }

    public function paginate()
    {
        return 123;
    }

}
