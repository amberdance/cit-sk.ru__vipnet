<?php

use App\Lib\Constant;
use App\Models\User\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{

    public function up()
    {
        (new RolesAndPermissionsSeeder)->run();

        foreach (User::all() as $user) {
            if ($user->login == "super1") {
                $user->login = "admin";
                $user->assignRole(Constant::ADMIN_ROLE);
                $user->save();
            }

            if (strpos($user->login, "user") !== false) {
                $user->assignRole(Constant::USER_ROLE);
            }
        }

        // Create root user here
        $usersList = require_once storage_path("app/users.php");

        $user             = new User();
        $user->login      = Constant::ROOT_ROLE;
        $user->first_name = Constant::ROOT_ROLE;
        $user->last_name  = Constant::ROOT_ROLE;
        $user->password   = Hash::make($usersList[Constant::ROOT_ROLE]);
        $user->assignRole(Constant::ROOT_ROLE);
        $user->save();
    }

    public function down()
    {
        //
    }
};
