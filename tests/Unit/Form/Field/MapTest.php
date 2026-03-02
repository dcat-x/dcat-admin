<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field;
use Dcat\Admin\Form\Field\Map;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class MapTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    // -------------------------------------------------------
    // Class existence and inheritance
    // -------------------------------------------------------

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(Map::class));
    }

    public function test_extends_field(): void
    {
        $this->assertTrue(is_subclass_of(Map::class, Field::class));
    }

    // -------------------------------------------------------
    // Method existence
    // -------------------------------------------------------

    public function test_height_method_exists(): void
    {
        $this->assertTrue(method_exists(Map::class, 'height'));
    }

    public function test_google_method_exists(): void
    {
        $this->assertTrue(method_exists(Map::class, 'google'));
    }

    public function test_tencent_method_exists(): void
    {
        $this->assertTrue(method_exists(Map::class, 'tencent'));
    }

    public function test_yandex_method_exists(): void
    {
        $this->assertTrue(method_exists(Map::class, 'yandex'));
    }

    public function test_baidu_method_exists(): void
    {
        $this->assertTrue(method_exists(Map::class, 'baidu'));
    }

    public function test_amap_method_exists(): void
    {
        $this->assertTrue(method_exists(Map::class, 'amap'));
    }

    public function test_require_assets_method_exists(): void
    {
        $this->assertTrue(method_exists(Map::class, 'requireAssets'));
    }

    public function test_get_using_map_method_exists(): void
    {
        $this->assertTrue(method_exists(Map::class, 'getUsingMap'));
    }

    // -------------------------------------------------------
    // Default property values via reflection
    // -------------------------------------------------------

    public function test_height_default_is_300px(): void
    {
        $reflection = new \ReflectionClass(Map::class);
        $properties = $reflection->getDefaultProperties();

        $this->assertArrayHasKey('height', $properties);
        $this->assertSame('300px', $properties['height']);
    }

    public function test_column_default_is_array(): void
    {
        $reflection = new \ReflectionClass(Map::class);
        $properties = $reflection->getDefaultProperties();

        $this->assertArrayHasKey('column', $properties);
        $this->assertIsArray($properties['column']);
    }

    // -------------------------------------------------------
    // Method visibility
    // -------------------------------------------------------

    public function test_height_is_public(): void
    {
        $method = new \ReflectionMethod(Map::class, 'height');
        $this->assertTrue($method->isPublic());
    }

    public function test_google_is_public(): void
    {
        $method = new \ReflectionMethod(Map::class, 'google');
        $this->assertTrue($method->isPublic());
    }

    public function test_tencent_is_public(): void
    {
        $method = new \ReflectionMethod(Map::class, 'tencent');
        $this->assertTrue($method->isPublic());
    }

    public function test_yandex_is_public(): void
    {
        $method = new \ReflectionMethod(Map::class, 'yandex');
        $this->assertTrue($method->isPublic());
    }

    public function test_baidu_is_public(): void
    {
        $method = new \ReflectionMethod(Map::class, 'baidu');
        $this->assertTrue($method->isPublic());
    }

    public function test_amap_is_public(): void
    {
        $method = new \ReflectionMethod(Map::class, 'amap');
        $this->assertTrue($method->isPublic());
    }

    public function test_require_assets_is_public_and_static(): void
    {
        $method = new \ReflectionMethod(Map::class, 'requireAssets');
        $this->assertTrue($method->isPublic());
        $this->assertTrue($method->isStatic());
    }

    public function test_get_using_map_is_protected_and_static(): void
    {
        $method = new \ReflectionMethod(Map::class, 'getUsingMap');
        $this->assertTrue($method->isProtected());
        $this->assertTrue($method->isStatic());
    }

    // -------------------------------------------------------
    // Height method parameter
    // -------------------------------------------------------

    public function test_height_has_string_parameter(): void
    {
        $method = new \ReflectionMethod(Map::class, 'height');
        $params = $method->getParameters();

        $this->assertCount(1, $params);
        $this->assertSame('height', $params[0]->getName());

        $type = $params[0]->getType();
        $this->assertNotNull($type);
        $this->assertSame('string', $type->getName());
    }

    // -------------------------------------------------------
    // Render method
    // -------------------------------------------------------

    public function test_render_method_exists(): void
    {
        $this->assertTrue(method_exists(Map::class, 'render'));
    }

    public function test_render_is_public(): void
    {
        $method = new \ReflectionMethod(Map::class, 'render');
        $this->assertTrue($method->isPublic());
    }
}
