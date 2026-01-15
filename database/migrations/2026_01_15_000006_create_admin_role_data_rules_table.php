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
        $table = config('admin.database.role_data_rules_table', 'admin_role_data_rules');

        Schema::create($table, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('role_id')->index();
            $table->bigInteger('data_rule_id')->index();
            $table->timestamps();

            $table->unique(['role_id', 'data_rule_id']);
        });
    }

    public function down()
    {
        $table = config('admin.database.role_data_rules_table', 'admin_role_data_rules');
        Schema::dropIfExists($table);
    }
};
