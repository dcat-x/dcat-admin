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
        $table = config('admin.database.departments_table', 'admin_departments');

        Schema::create($table, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('parent_id')->default(0)->index();
            $table->string('path', 255)->default('')->index()->comment('层级路径，如 /1/5/12/');
            $table->string('name', 100)->comment('部门名称');
            $table->string('code', 100)->unique()->comment('部门编码');
            $table->integer('order')->default(0)->comment('排序');
            $table->tinyInteger('status')->default(1)->comment('状态：1启用 0禁用');
            $table->timestamps();
        });
    }

    public function down()
    {
        $table = config('admin.database.departments_table', 'admin_departments');
        Schema::dropIfExists($table);
    }
};
