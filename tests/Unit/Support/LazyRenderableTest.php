<?php

namespace Dcat\Admin\Tests\Unit\Support;

use Dcat\Admin\Contracts\LazyRenderable as LazyRenderableContract;
use Dcat\Admin\Support\LazyRenderable;
use Dcat\Admin\Tests\TestCase;

class LazyRenderableTest extends TestCase
{
    public function test_class_is_abstract(): void
    {
        $ref = new \ReflectionClass(LazyRenderable::class);
        $this->assertTrue($ref->isAbstract());
    }

    public function test_implements_lazy_renderable_contract(): void
    {
        $implements = class_implements(LazyRenderable::class);

        $this->assertContains(LazyRenderableContract::class, $implements);
    }

    public function test_constructor_accepts_payload(): void
    {
        $instance = new ConcreteLazyRenderable(['key' => 'value']);

        $ref = new \ReflectionProperty(LazyRenderable::class, 'payload');
        $ref->setAccessible(true);
        $payload = $ref->getValue($instance);

        $this->assertSame('value', $payload['key'] ?? null);
    }

    public function test_magic_get_returns_payload_value(): void
    {
        $instance = new ConcreteLazyRenderable(['name' => 'test']);
        $this->assertSame('test', $instance->name);
    }

    public function test_magic_get_returns_null_for_missing_key(): void
    {
        $instance = new ConcreteLazyRenderable([]);
        $this->assertNull($instance->nonexistent);
    }

    public function test_make_static_factory(): void
    {
        $instance = ConcreteLazyRenderable::make(['foo' => 'bar']);
        $this->assertInstanceOf(LazyRenderable::class, $instance);
        $this->assertSame('bar', $instance->foo);
    }

    public function test_has_js_property(): void
    {
        $ref = new \ReflectionProperty(LazyRenderable::class, 'js');
        $ref->setAccessible(true);
        $this->assertIsArray($ref->getDefaultValue());
    }

    public function test_has_css_property(): void
    {
        $ref = new \ReflectionProperty(LazyRenderable::class, 'css');
        $ref->setAccessible(true);
        $this->assertIsArray($ref->getDefaultValue());
    }

    public function test_require_assets_method_signature(): void
    {
        $method = new \ReflectionMethod(LazyRenderable::class, 'requireAssets');

        $this->assertSame(0, $method->getNumberOfParameters());
    }
}

class ConcreteLazyRenderable extends LazyRenderable
{
    public function render()
    {
        return '<div>rendered</div>';
    }
}
