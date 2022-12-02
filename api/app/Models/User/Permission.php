<?php

namespace App\Models\User;

/**
 *  @property int $id
 *  @property string $name
 *  @property string $description
 */

class Permission extends \Spatie\Permission\Models\Permission

{

    protected $hidden = [
        'guard_name',
        'created_at',
        'updated_at',
        'pivot',
    ];

}
