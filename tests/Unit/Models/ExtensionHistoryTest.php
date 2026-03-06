<?php

namespace Dcat\Admin\Tests\Unit\Models;

use Dcat\Admin\Models\ExtensionHistory;
use Dcat\Admin\Tests\TestCase;

class ExtensionHistoryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('admin.database.extension_histories_table', 'admin_extension_histories');
    }

    public function test_fillable_attributes(): void
    {
        $history = new ExtensionHistory;

        $fillable = $history->getFillable();

        $this->assertContains('name', $fillable);
        $this->assertContains('type', $fillable);
        $this->assertContains('version', $fillable);
        $this->assertContains('detail', $fillable);
        $this->assertCount(4, $fillable);
    }

    public function test_table_name_from_config(): void
    {
        $this->app['config']->set('admin.database.extension_histories_table', 'custom_extension_histories');

        $history = new ExtensionHistory;

        $this->assertSame('custom_extension_histories', $history->getTable());
    }

    public function test_table_name_defaults_to_admin_extension_histories(): void
    {
        $this->app['config']->set('admin.database.extension_histories_table', null);

        $history = new ExtensionHistory;

        $this->assertSame('admin_extension_histories', $history->getTable());
    }

    public function test_connection_from_config(): void
    {
        $this->app['config']->set('admin.database.connection', 'mysql');

        $history = new ExtensionHistory;

        $this->assertSame('mysql', $history->getConnectionName());
    }

    public function test_connection_defaults_to_database_default(): void
    {
        $this->app['config']->set('admin.database.connection', '');
        $this->app['config']->set('database.default', 'testing');

        $history = new ExtensionHistory;

        $this->assertSame('testing', $history->getConnectionName());
    }

    public function test_creation_with_attributes(): void
    {
        $history = new ExtensionHistory([
            'name' => 'test-extension',
            'type' => 1,
            'version' => '1.0.0',
            'detail' => 'Initial installation',
        ]);

        $this->assertInstanceOf(ExtensionHistory::class, $history);
        $this->assertSame('test-extension', $history->name);
        $this->assertSame(1, $history->type);
        $this->assertSame('1.0.0', $history->version);
        $this->assertSame('Initial installation', $history->detail);
    }
}
