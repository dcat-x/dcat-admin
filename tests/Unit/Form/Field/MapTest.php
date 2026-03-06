<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

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
    // Default property values via reflection
    // -------------------------------------------------------

    public function test_height_default_is_300px(): void
    {
        $reflection = new \ReflectionClass(Map::class);
        $properties = $reflection->getDefaultProperties();

        $this->assertSame('300px', $properties['height'] ?? null);
    }

    public function test_column_default_is_array(): void
    {
        $reflection = new \ReflectionClass(Map::class);
        $properties = $reflection->getDefaultProperties();

        $this->assertIsArray($properties['column'] ?? null);
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

    public function test_render_is_public(): void
    {
        $method = new \ReflectionMethod(Map::class, 'render');
        $this->assertTrue($method->isPublic());
    }
}
