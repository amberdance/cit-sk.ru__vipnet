<?php

namespace App\Models\Application;

use App\Models\Signature;
use App\Interfaces\ResourceModel;
use Illuminate\Database\Eloquent\Model;
use App\Models\Organization\Organization;
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
class Application extends Model implements ResourceModel
{

    protected $guarded = [];
    protected $casts   = [
        "created_at"     => "datetime:d.m.Y",
        "reception_date" => "datetime:d.m.Y",
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

    public function paginate(){
        return 123;
    }

}
