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
        $table = config('admin.database.data_rules_table', 'admin_data_rules');

        Schema::create($table, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('menu_id')->index()->comment('关联菜单ID');
            $table->string('name', 100)->comment('规则名称');
            $table->string('field', 100)->comment('数据表字段');
            $table->string('condition', 20)->comment('条件');
            $table->string('value', 255)->nullable()->comment('规则值');
            $table->string('value_type', 20)->default('fixed')->comment('值类型：fixed/variable');
            $table->string('scope', 20)->default('row')->comment('作用域：row/column/form');
            $table->tinyInteger('status')->default(1)->comment('状态：1启用 0禁用');
            $table->integer('order')->default(0)->comment('排序');
            $table->timestamps();
        });
    }

    public function down()
    {
        $table = config('admin.database.data_rules_table', 'admin_data_rules');
        Schema::dropIfExists($table);
    }
};
