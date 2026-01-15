<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function getConnection()
    {
        return config('admin.database.connection') ?: config('database.default');
    }

    public function up()
    {
        $table = config('admin.database.department_roles_table', 'admin_department_roles');

        Schema::create($table, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('department_id')->index();
            $table->bigInteger('role_id')->index();
            $table->timestamps();

            $table->unique(['department_id', 'role_id']);
        });
    }

    public function down()
    {
        $table = config('admin.database.department_roles_table', 'admin_department_roles');
        Schema::dropIfExists($table);
    }
};
