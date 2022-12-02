<?php

namespace App\Models\User;

/**
 *  @property int $id
 *  @property string $name
 *  @property string $description
 */

class Role extends \Spatie\Permission\Models\Role

{

    protected $hidden = [
        'guard_name',
        'created_at',
        'updated_at',
        'pivot',
    ];

}
