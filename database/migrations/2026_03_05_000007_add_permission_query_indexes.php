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
        $menuTable = config('admin.database.menu_table', 'admin_menu');
        $dataRulesTable = config('admin.database.data_rules_table', 'admin_data_rules');
        $roleDataRulesTable = config('admin.database.role_data_rules_table', 'admin_role_data_rules');

        Schema::table($menuTable, function (Blueprint $table) {
            $table->index('uri', 'admin_menu_uri_index');
        });

        Schema::table($dataRulesTable, function (Blueprint $table) {
            $table->index(['menu_id', 'status', 'order'], 'admin_data_rules_menu_status_order_index');
            $table->index(['menu_id', 'status', 'scope'], 'admin_data_rules_menu_status_scope_index');
        });

        Schema::table($roleDataRulesTable, function (Blueprint $table) {
            $table->index(['data_rule_id', 'role_id'], 'admin_role_data_rules_data_rule_role_index');
        });
    }

    public function down()
    {
        $menuTable = config('admin.database.menu_table', 'admin_menu');
        $dataRulesTable = config('admin.database.data_rules_table', 'admin_data_rules');
        $roleDataRulesTable = config('admin.database.role_data_rules_table', 'admin_role_data_rules');

        Schema::table($menuTable, function (Blueprint $table) {
            $table->dropIndex('admin_menu_uri_index');
        });

        Schema::table($dataRulesTable, function (Blueprint $table) {
            $table->dropIndex('admin_data_rules_menu_status_order_index');
            $table->dropIndex('admin_data_rules_menu_status_scope_index');
        });

        Schema::table($roleDataRulesTable, function (Blueprint $table) {
            $table->dropIndex('admin_role_data_rules_data_rule_role_index');
        });
    }
};
