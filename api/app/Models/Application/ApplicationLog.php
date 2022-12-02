<?php

namespace App\Models\Application;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int id
 * @property int application_id
 * @property int event_id
 * @property string user_id

 */
class ApplicationLog extends Model
{

    protected $guarded = [];

    public function application(): HasOne
    {
        return $this->hasOne(Application::class, "id", "application_id");
    }

    public function event(): HasOne
    {
        return $this->hasOne(ApplicationEvent::class, "id", "event_id");
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, "id", "user_id");
    }

}
