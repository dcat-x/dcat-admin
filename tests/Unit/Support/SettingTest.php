<?php

namespace Dcat\Admin\Tests\Unit\Support;

use Dcat\Admin\Support\Setting;
use Dcat\Admin\Tests\TestCase;

class SettingTest extends TestCase
{
    public function test_constructor_creates_instance(): void
    {
        $setting = new Setting;

        $this->assertInstanceOf(Setting::class, $setting);
    }

    public function test_constructor_with_initial_attributes(): void
    {
        $setting = new Setting(['key1' => 'value1', 'key2' => 'value2']);

        $this->assertSame('value1', $setting->get('key1'));
        $this->assertSame('value2', $setting->get('key2'));
    }

    public function test_get_returns_value(): void
    {
        $setting = new Setting(['site_name' => 'My Admin']);

        $this->assertSame('My Admin', $setting->get('site_name'));
    }

    public function test_get_returns_default_for_missing_key(): void
    {
        $setting = new Setting;

        $this->assertNull($setting->get('nonexistent'));
        $this->assertSame('default', $setting->get('nonexistent', 'default'));
    }

    public function test_set_with_key_value(): void
    {
        $setting = new Setting;

        $result = $setting->set('site_name', 'Test Site');

        $this->assertSame($setting, $result);
        $this->assertSame('Test Site', $setting->get('site_name'));
    }

    public function test_set_with_array(): void
    {
        $setting = new Setting;

        $result = $setting->set([
            'site_name' => 'Test Site',
            'site_url' => 'https://example.com',
        ]);

        $this->assertSame($setting, $result);
        $this->assertSame('Test Site', $setting->get('site_name'));
        $this->assertSame('https://example.com', $setting->get('site_url'));
    }

    public function test_get_array_returns_array_from_json(): void
    {
        $setting = new Setting(['tags' => json_encode(['php', 'laravel'])]);

        $result = $setting->getArray('tags');

        $this->assertIsArray($result);
        $this->assertSame(['php', 'laravel'], $result);
    }

    public function test_get_array_returns_empty_for_missing_key(): void
    {
        $setting = new Setting;

        $result = $setting->getArray('nonexistent');

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function test_get_array_returns_array_value_directly(): void
    {
        $setting = new Setting(['items' => ['a', 'b', 'c']]);

        $result = $setting->getArray('items');

        $this->assertSame(['a', 'b', 'c'], $result);
    }

    public function test_get_array_returns_empty_for_falsy_value(): void
    {
        $setting = new Setting(['empty_key' => '']);

        $result = $setting->getArray('empty_key');

        $this->assertSame([], $result);
    }

    public function test_get_array_returns_empty_for_null_value(): void
    {
        $setting = new Setting(['null_key' => null]);

        $result = $setting->getArray('null_key');

        $this->assertSame([], $result);
    }

    public function test_add_many_merges_values(): void
    {
        $setting = new Setting(['items' => json_encode(['a', 'b'])]);

        $result = $setting->addMany('items', ['c', 'd']);

        $this->assertSame($setting, $result);
        $items = $setting->getArray('items');
        $this->assertContains('a', $items);
        $this->assertContains('c', $items);
        $this->assertContains('d', $items);
    }

    public function test_add_many_creates_new_array_for_missing_key(): void
    {
        $setting = new Setting;

        $setting->addMany('new_items', ['x', 'y']);

        $result = $setting->getArray('new_items');

        $this->assertSame(['x', 'y'], $result);
    }

    public function test_set_overwrites_existing_value(): void
    {
        $setting = new Setting(['key' => 'old']);

        $setting->set('key', 'new');

        $this->assertSame('new', $setting->get('key'));
    }

    public function test_get_with_dot_notation(): void
    {
        $setting = new Setting(['parent' => ['child' => 'value']]);

        $this->assertSame('value', $setting->get('parent.child'));
    }
}
