<?php

namespace Database\Seeders;

use App\Lib\Constant;
use App\Models\User\Permission;
use App\Models\User\Role;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $roles = [
            Constant::ROOT_ROLE  => 'Избранный',
            Constant::ADMIN_ROLE => 'Администратор',
            Constant::USER_ROLE  => 'Пользователь',
        ];

        $permissions = [
            'assign roles'         => 'Назначать роль',
            'assign permissions'   => 'Назначать права',

            'user.viewAny'         => 'Просмотр пользователей',
            'user.create'          => 'Создание пользователя',
            'user.delete'          => 'Удаление пользователя',
            'user.update'          => 'Редактирование пользователя',

            'application.viewAny'  => 'Просмотр списка заявок',
            'application.create'   => 'Создание заявки',
            'application.delete'   => 'Удаление заявки',
            'application.update'   => 'Редактирование заявки ',

            'organization.viewAny' => 'Просмотр списка организаций',
            'organization.create'  => 'Создание организации',
            'organization.delete'  => 'Удаление организации',
            'organization.update'  => 'Редактирование организации',

        ];

        foreach ($roles as $role => $description) {
            Role::create([
                'name'        => $role,
                'description' => $description,
            ]);
        }

        foreach ($permissions as $permission => $description) {
            Permission::create([
                'name'        => $permission,
                'description' => $description,
            ]);
        }

        Role::findByName(Constant::ADMIN_ROLE)->givePermissionTo([
            'user.viewAny',
            'user.create',
            'user.delete',
            'user.update',

            'application.viewAny',
            'application.create',
            'application.delete',
            'application.update',

            'organization.viewAny',
            'organization.create',
            'organization.delete',
            'organization.update',
        ]);

        Role::findByName(Constant::USER_ROLE)->givePermissionTo([
            'application.viewAny',
            'application.create',
            'application.delete',
            'application.update',

            'organization.viewAny',
            'organization.create',
            'organization.delete',
            'organization.update',
        ]);

    }
}
