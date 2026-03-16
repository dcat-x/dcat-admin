<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Widgets;

use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Widgets\Lazy;
use Mockery;

class LazyTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_constructor_without_renderable(): void
    {
        $lazy = new Lazy;
        $this->assertInstanceOf(Lazy::class, $lazy);
    }

    public function test_load_default_true(): void
    {
        $lazy = new Lazy;
        $ref = new \ReflectionProperty($lazy, 'load');
        $ref->setAccessible(true);
        $this->assertTrue($ref->getValue($lazy));
    }

    public function test_load_false(): void
    {
        $lazy = new Lazy(null, false);
        $ref = new \ReflectionProperty($lazy, 'load');
        $ref->setAccessible(true);
        $this->assertFalse($ref->getValue($lazy));
    }

    public function test_load_method(): void
    {
        $lazy = new Lazy;
        $result = $lazy->load(false);
        $this->assertSame($lazy, $result);
        $ref = new \ReflectionProperty($lazy, 'load');
        $ref->setAccessible(true);
        $this->assertFalse($ref->getValue($lazy));
    }

    public function test_element_class_starts_with_lazy(): void
    {
        $lazy = new Lazy;
        $this->assertStringStartsWith('lazy-', $lazy->getElementClass());
    }

    public function test_html_contains_div(): void
    {
        $lazy = new Lazy;
        $html = $lazy->html();
        $this->assertStringContainsString('<div', $html);
        $this->assertStringContainsString('lazy-box', $html);
    }
}
