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
        $table = config('admin.database.department_users_table', 'admin_department_users');

        Schema::create($table, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('department_id')->index();
            $table->bigInteger('user_id')->index();
            $table->tinyInteger('is_primary')->default(0)->comment('是否主部门：1是 0否');
            $table->timestamps();

            $table->unique(['department_id', 'user_id']);
        });
    }

    public function down()
    {
        $table = config('admin.database.department_users_table', 'admin_department_users');
        Schema::dropIfExists($table);
    }
};
