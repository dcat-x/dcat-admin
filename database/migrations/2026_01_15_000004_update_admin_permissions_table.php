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
        $table = config('admin.database.permissions_table', 'admin_permissions');

        Schema::table($table, function (Blueprint $table) {
            $table->tinyInteger('type')->default(1)->after('slug')
                ->comment('权限类型：1菜单权限 2按钮权限 3数据权限');
            $table->string('permission_key', 100)->nullable()->after('type')
                ->comment('权限标识，如 user:add');
            $table->bigInteger('menu_id')->nullable()->after('permission_key')
                ->comment('所属菜单ID（按钮权限时）');

            $table->index('type');
            $table->index('permission_key');
            $table->index('menu_id');
        });
    }

    public function down()
    {
        $table = config('admin.database.permissions_table', 'admin_permissions');

        Schema::table($table, function (Blueprint $table) {
            $table->dropIndex(['type']);
            $table->dropIndex(['permission_key']);
            $table->dropIndex(['menu_id']);
            $table->dropColumn(['type', 'permission_key', 'menu_id']);
        });
    }
};
