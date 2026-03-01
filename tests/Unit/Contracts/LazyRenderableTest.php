<?php

namespace Dcat\Admin\Tests\Unit\Contracts;

use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Tests\TestCase;

class LazyRenderableTest extends TestCase
{
    public function test_interface_exists(): void
    {
        $this->assertTrue(interface_exists(LazyRenderable::class));
    }

    public function test_has_get_url_method(): void
    {
        $this->assertTrue(method_exists(LazyRenderable::class, 'getUrl'));
    }

    public function test_has_render_method(): void
    {
        $this->assertTrue(method_exists(LazyRenderable::class, 'render'));
    }

    public function test_anonymous_implementation(): void
    {
        $instance = new class implements LazyRenderable
        {
            public function getUrl()
            {
                return '/test-url';
            }

            public function render()
            {
                return '<div>test</div>';
            }
        };

        $this->assertInstanceOf(LazyRenderable::class, $instance);
        $this->assertSame('/test-url', $instance->getUrl());
        $this->assertSame('<div>test</div>', $instance->render());
    }
}
