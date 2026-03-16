<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Support;

use Dcat\Admin\Support\ComposerProperty;
use Dcat\Admin\Tests\TestCase;

class ComposerPropertyTest extends TestCase
{
    public function test_constructor_with_empty_attributes(): void
    {
        $property = new ComposerProperty;

        $this->assertSame([], $property->toArray());
    }

    public function test_constructor_with_attributes(): void
    {
        $attrs = ['name' => 'vendor/package', 'version' => '1.0.0'];
        $property = new ComposerProperty($attrs);

        $this->assertSame($attrs, $property->toArray());
    }

    public function test_get_returns_value(): void
    {
        $property = new ComposerProperty(['name' => 'vendor/package']);

        $this->assertSame('vendor/package', $property->get('name'));
    }

    public function test_get_returns_default_for_missing_key(): void
    {
        $property = new ComposerProperty;

        $this->assertNull($property->get('missing'));
        $this->assertSame('default', $property->get('missing', 'default'));
    }

    public function test_get_with_dot_notation(): void
    {
        $property = new ComposerProperty([
            'extra' => [
                'dcat-admin' => [
                    'title' => 'My Extension',
                ],
            ],
        ]);

        $this->assertSame('My Extension', $property->get('extra.dcat-admin.title'));
    }

    public function test_set_returns_new_instance(): void
    {
        $original = new ComposerProperty(['name' => 'vendor/package']);
        $modified = $original->set('version', '2.0.0');

        $this->assertNotSame($original, $modified);
        $this->assertInstanceOf(ComposerProperty::class, $modified);
    }

    public function test_set_does_not_modify_original(): void
    {
        $original = new ComposerProperty(['name' => 'vendor/package']);
        $original->set('version', '2.0.0');

        $this->assertNull($original->get('version'));
        $this->assertSame('vendor/package', $original->get('name'));
    }

    public function test_set_value_is_present_in_new_instance(): void
    {
        $original = new ComposerProperty(['name' => 'vendor/package']);
        $modified = $original->set('version', '2.0.0');

        $this->assertSame('2.0.0', $modified->get('version'));
        $this->assertSame('vendor/package', $modified->get('name'));
    }

    public function test_delete_returns_new_instance_without_key(): void
    {
        $original = new ComposerProperty(['name' => 'vendor/package', 'version' => '1.0.0']);
        $modified = $original->delete('version');

        $this->assertNotSame($original, $modified);
        $this->assertNull($modified->get('version'));
        $this->assertSame('vendor/package', $modified->get('name'));
    }

    public function test_delete_does_not_modify_original(): void
    {
        $original = new ComposerProperty(['name' => 'vendor/package', 'version' => '1.0.0']);
        $original->delete('version');

        $this->assertSame('1.0.0', $original->get('version'));
    }

    public function test_magic_get_translates_underscores_to_hyphens(): void
    {
        $property = new ComposerProperty([
            'require-dev' => ['phpunit/phpunit' => '^10.0'],
        ]);

        $this->assertSame(['phpunit/phpunit' => '^10.0'], $property->require_dev);
    }

    public function test_magic_get_for_simple_properties(): void
    {
        $property = new ComposerProperty([
            'name' => 'vendor/package',
            'description' => 'A test package',
            'version' => '1.2.3',
        ]);

        $this->assertSame('vendor/package', $property->name);
        $this->assertSame('A test package', $property->description);
        $this->assertSame('1.2.3', $property->version);
    }

    public function test_to_json_returns_json_string(): void
    {
        $property = new ComposerProperty(['name' => 'vendor/package']);
        $json = $property->toJson();

        $this->assertIsString($json);
        $decoded = json_decode($json, true);
        $this->assertSame('vendor/package', $decoded['name']);
    }

    public function test_to_array_returns_all_attributes(): void
    {
        $attrs = [
            'name' => 'vendor/package',
            'version' => '1.0.0',
            'description' => 'Test',
        ];
        $property = new ComposerProperty($attrs);

        $this->assertSame($attrs, $property->toArray());
    }

    public function test_set_with_dot_notation(): void
    {
        $property = new ComposerProperty(['extra' => []]);
        $modified = $property->set('extra.dcat-admin.title', 'My Extension');

        $this->assertSame('My Extension', $modified->get('extra.dcat-admin.title'));
    }
}
