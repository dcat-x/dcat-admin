<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Models;

use Dcat\Admin\Models\Setting;
use Dcat\Admin\Tests\TestCase;

class SettingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('admin.database.settings_table', 'admin_settings');
    }

    public function test_primary_key_is_slug(): void
    {
        $setting = new Setting;

        $this->assertSame('slug', $setting->getKeyName());
    }

    public function test_incrementing_is_false(): void
    {
        $setting = new Setting;

        $this->assertFalse($setting->getIncrementing());
    }

    public function test_fillable_attributes(): void
    {
        $setting = new Setting;

        $fillable = $setting->getFillable();

        $this->assertContains('slug', $fillable);
        $this->assertContains('value', $fillable);
        $this->assertCount(2, $fillable);
    }

    public function test_table_name_from_config(): void
    {
        $this->app['config']->set('admin.database.settings_table', 'custom_settings');

        $setting = new Setting;

        $this->assertSame('custom_settings', $setting->getTable());
    }

    public function test_table_name_defaults_to_admin_settings(): void
    {
        $this->app['config']->set('admin.database.settings_table', null);

        $setting = new Setting;

        $this->assertSame('admin_settings', $setting->getTable());
    }

    public function test_connection_from_config(): void
    {
        $this->app['config']->set('admin.database.connection', 'mysql');

        $setting = new Setting;

        $this->assertSame('mysql', $setting->getConnectionName());
    }

    public function test_creation_with_attributes(): void
    {
        $setting = new Setting([
            'slug' => 'site.title',
            'value' => 'My Admin',
        ]);

        $this->assertInstanceOf(Setting::class, $setting);
        $this->assertSame('site.title', $setting->slug);
        $this->assertSame('My Admin', $setting->value);
    }
}
