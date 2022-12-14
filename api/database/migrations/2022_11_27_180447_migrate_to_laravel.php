<?php

use App\Models\Signature;
use App\Models\User\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {

        /********************************
         *         SHARED
         *********************************/

        Schema::rename("refs", "organizations");
        Schema::rename("applist", "applications");
        Schema::rename("refs_note", "organization_notes_deprecated");
        Schema::rename("applist_log", "application_logs_deprecated");
        Schema::rename("event", "events__deprecated");
        Schema::rename("signature_type", "signatures");
        Schema::drop("responsibles");
        Schema::drop("applist_history");
        Schema::drop("connections");
        Schema::drop("personal_access_tokens");

        /********************************
         *         ORGANIZATIONS
         *********************************/

        DB::statement("ALTER TABLE organizations CHANGE created created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP");
        DB::statement("ALTER TABLE organization_notes_deprecated CHANGE created created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP");

        Schema::table('organizations', function (Blueprint $table) {
            $table->text("note")->nullable();
            // $table->integer("note_id")->nullable();
            // $table->foreign("note_id")->references("id")->on("organization_notes_deprecated");
        });

        Schema::table('organization_notes_deprecated', function (Blueprint $table) {
            $table->renameColumn('ref_id', "organization_id");
            $table->longText("content")->change();
            $table->renameColumn('content', "description");
            // $table->foreign("organization_id")->references("id")->on("organizations");
        });

        /********************************
         *             USERS
         *********************************/
        Schema::table('users', function (Blueprint $table) {
            $table->after("id", fn($table) => $table->timestamps());
            $table->after('password', function (Blueprint $table) {
                $table->string('first_name');
                $table->string('last_name')->nullable();
                $table->string('patronymic')->nullable();
                $table->string("phone", 14)->nullable();
                $table->string('email')->nullable();
                $table->boolean('is_active')->default(true);
            });
            $table->dropColumn('role');
            $table->dropColumn('responsible_id');
            $table->dropColumn('is_blocked');
        });

        $users = require_once storage_path("app/users.php");
        $i     = 1;

        foreach ($users as $login => $password) {
            try {
                /** @var User */
                $user             = User::where("login", $login)->firstOrFail();
                $user->created_at = Carbon::now();
                $user->first_name = is_numeric(strpos($user->login, "super")) ? "Администратор №" . $i++ : "Пользователь №" . $i++;
                $user->password   = Hash::make($password);
                $user->save();
            } catch (Exception $e) {
                continue;
            }
        }

        /********************************
         *         APPLICATION
         *********************************/

        DB::statement("DELETE FROM applications WHERE reference_id IS NULL");
        DB::statement("DELETE FROM applications WHERE reception_date = '0000-00-00 00:00:00'");
        DB::statement("ALTER TABLE applications MODIFY is_active tinyint(1) default 1 AFTER person_count");

        Schema::table('applications', function (Blueprint $table) {
            $table->renameColumn("signature_type_id", "signature_id");
            $table->renameColumn("reference_id", "organization_id");
            $table->longText("note")->change()->nullable();
            $table->timestamp("reception_date")->change();
            $table->after("id", fn(Blueprint $table) => $table->timestamps());
            $table->foreign("signature_id")->references("id")->on("signatures");
            $table->foreign("organization_id")->references("id")->on("organizations");
        });

        foreach (DB::table("applications")->get() as $key => $value) {
            DB::table("applications")->where("id", $value->id)->update(
                [
                    "created_at" => $value->created,
                ]
            );
        }

        Schema::table("applications", fn(Blueprint $table) => $table->dropColumn("created"));

        Schema::table('application_logs_deprecated', function (Blueprint $table) {
            $table->dropColumn('created');
            $table->renameColumn("entity_id", "application_id");
            // $table->foreign("event_id")->references("id")->on("events");
            // $table->foreign("user_id")->references("id")->on("users");
        });

        /********************************
         *         SIGNATURES
         *********************************/

        foreach (["Vipnet 11074", "Vipnet 2040"] as $key => $value) {
            $signature        = new Signature();
            $signature->label = $value;
            $signature->save();
        }

         /********************************
         *         CLEAN UP
         *********************************/

        DB::statement("UPDATE signatures SET label = CONCAT(UCASE(LEFT(label, 1)), SUBSTRING(label, 2));");
        DB::statement("UPDATE applications SET note = CONCAT(UCASE(LEFT(note, 1)), SUBSTRING(note, 2));");
    }

    public function down()
    {
        //
    }
};
