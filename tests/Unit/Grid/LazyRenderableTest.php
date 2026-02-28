<?php

namespace Dcat\Admin\Tests\Unit\Grid;

use Dcat\Admin\Support\LazyRenderable;
use Dcat\Admin\Tests\TestCase;

class ConcreteLazyRenderable extends LazyRenderable
{
    public function render()
    {
        return 'rendered';
    }
}

class LazyRenderableTest extends TestCase
{
    public function test_constructor_with_empty_payload(): void
    {
        $renderable = new ConcreteLazyRenderable;
        $this->assertInstanceOf(LazyRenderable::class, $renderable);
    }

    public function test_constructor_with_payload(): void
    {
        $renderable = new ConcreteLazyRenderable(['key' => 'value']);
        $this->assertEquals('value', $renderable->key);
    }

    public function test_magic_get_returns_payload_value(): void
    {
        $renderable = new ConcreteLazyRenderable(['name' => 'test']);
        $this->assertEquals('test', $renderable->name);
    }

    public function test_magic_get_returns_null_for_missing_key(): void
    {
        $renderable = new ConcreteLazyRenderable;
        $this->assertNull($renderable->nonexistent);
    }

    public function test_payload_merges_values(): void
    {
        $renderable = new ConcreteLazyRenderable(['a' => 1]);
        $result = $renderable->payload(['b' => 2]);

        $this->assertSame($renderable, $result);
        $this->assertEquals(1, $renderable->a);
        $this->assertEquals(2, $renderable->b);
    }

    public function test_payload_overwrites_existing_keys(): void
    {
        $renderable = new ConcreteLazyRenderable(['key' => 'old']);
        $renderable->payload(['key' => 'new']);
        $this->assertEquals('new', $renderable->key);
    }

    public function test_make_factory_method(): void
    {
        $renderable = ConcreteLazyRenderable::make(['foo' => 'bar']);
        $this->assertInstanceOf(ConcreteLazyRenderable::class, $renderable);
        $this->assertEquals('bar', $renderable->foo);
    }

    public function test_make_without_params(): void
    {
        $renderable = ConcreteLazyRenderable::make();
        $this->assertInstanceOf(ConcreteLazyRenderable::class, $renderable);
    }
}
