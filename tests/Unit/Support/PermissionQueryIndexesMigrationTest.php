<?php

namespace Dcat\Admin\Tests\Unit\Support;

use Dcat\Admin\Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PermissionQueryIndexesMigrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('admin_menu', function ($table) {
            $table->bigIncrements('id');
            $table->string('uri', 50)->nullable();
        });

        Schema::create('admin_data_rules', function ($table) {
            $table->bigIncrements('id');
            $table->bigInteger('menu_id');
            $table->tinyInteger('status')->default(1);
            $table->integer('order')->default(0);
            $table->string('scope', 20)->default('row');
        });

        Schema::create('admin_role_data_rules', function ($table) {
            $table->bigIncrements('id');
            $table->bigInteger('role_id');
            $table->bigInteger('data_rule_id');
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('admin_role_data_rules');
        Schema::dropIfExists('admin_data_rules');
        Schema::dropIfExists('admin_menu');

        parent::tearDown();
    }

    public function test_permission_query_indexes_migration_up_and_down(): void
    {
        $migration = require dirname(__DIR__, 3).'/database/migrations/2026_03_05_000007_add_permission_query_indexes.php';

        $migration->up();

        $menuIndexes = $this->getIndexNames('admin_menu');
        $dataRulesIndexes = $this->getIndexNames('admin_data_rules');
        $roleDataRulesIndexes = $this->getIndexNames('admin_role_data_rules');

        $this->assertContains('admin_menu_uri_index', $menuIndexes);
        $this->assertContains('admin_data_rules_menu_status_order_index', $dataRulesIndexes);
        $this->assertContains('admin_data_rules_menu_status_scope_index', $dataRulesIndexes);
        $this->assertContains('admin_role_data_rules_data_rule_role_index', $roleDataRulesIndexes);

        $migration->down();

        $menuIndexes = $this->getIndexNames('admin_menu');
        $dataRulesIndexes = $this->getIndexNames('admin_data_rules');
        $roleDataRulesIndexes = $this->getIndexNames('admin_role_data_rules');

        $this->assertNotContains('admin_menu_uri_index', $menuIndexes);
        $this->assertNotContains('admin_data_rules_menu_status_order_index', $dataRulesIndexes);
        $this->assertNotContains('admin_data_rules_menu_status_scope_index', $dataRulesIndexes);
        $this->assertNotContains('admin_role_data_rules_data_rule_role_index', $roleDataRulesIndexes);
    }

    protected function getIndexNames(string $table): array
    {
        $indexes = DB::select("PRAGMA index_list('$table')");

        return collect($indexes)->pluck('name')->all();
    }
}
