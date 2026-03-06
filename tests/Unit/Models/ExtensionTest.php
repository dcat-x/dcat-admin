<?php

namespace Dcat\Admin\Tests\Unit\Models;

use Dcat\Admin\Models\Extension;
use Dcat\Admin\Tests\TestCase;

class ExtensionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('admin.database.extensions_table', 'admin_extensions');
    }

    public function test_fillable_attributes(): void
    {
        $extension = new Extension;

        $fillable = $extension->getFillable();

        $this->assertContains('name', $fillable);
        $this->assertContains('is_enabled', $fillable);
        $this->assertContains('version', $fillable);
        $this->assertContains('options', $fillable);
        $this->assertCount(4, $fillable);
    }

    public function test_casts_options_as_json(): void
    {
        $extension = new Extension;

        $casts = $extension->getCasts();

        $this->assertSame('json', $casts['options'] ?? null);
    }

    public function test_table_name_from_config(): void
    {
        $this->app['config']->set('admin.database.extensions_table', 'custom_extensions');

        $extension = new Extension;

        $this->assertSame('custom_extensions', $extension->getTable());
    }

    public function test_table_name_defaults_to_admin_extensions(): void
    {
        $this->app['config']->set('admin.database.extensions_table', null);

        $extension = new Extension;

        $this->assertSame('admin_extensions', $extension->getTable());
    }

    public function test_connection_from_config(): void
    {
        $this->app['config']->set('admin.database.connection', 'mysql');

        $extension = new Extension;

        $this->assertSame('mysql', $extension->getConnectionName());
    }

    public function test_connection_defaults_to_database_default(): void
    {
        $this->app['config']->set('admin.database.connection', '');
        $this->app['config']->set('database.default', 'testing');

        $extension = new Extension;

        $this->assertSame('testing', $extension->getConnectionName());
    }

    public function test_creation_with_attributes(): void
    {
        $extension = new Extension([
            'name' => 'test-extension',
            'is_enabled' => 1,
            'version' => '1.0.0',
            'options' => ['key' => 'value'],
        ]);

        $this->assertInstanceOf(Extension::class, $extension);
        $this->assertSame('test-extension', $extension->name);
        $this->assertSame(1, $extension->is_enabled);
        $this->assertSame('1.0.0', $extension->version);
    }
}
