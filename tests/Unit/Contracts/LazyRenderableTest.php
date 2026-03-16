<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Contracts;

use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Tests\TestCase;

class LazyRenderableTest extends TestCase
{
    public function test_interface_methods_are_declared(): void
    {
        $ref = new \ReflectionClass(LazyRenderable::class);
        $methods = array_map(fn (\ReflectionMethod $method) => $method->getName(), $ref->getMethods());

        $this->assertContains('getUrl', $methods);
        $this->assertContains('render', $methods);
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
