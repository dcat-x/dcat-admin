<?php

namespace Dcat\Admin\Tests\Unit\Grid\Tools;

use Dcat\Admin\Grid\Tools\Paginator;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Contracts\Support\Renderable;

class PaginatorTest extends TestCase
{
    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    // -------------------------------------------------------------------------
    // Class structure
    // -------------------------------------------------------------------------

    public function test_constructor_accepts_grid_parameter(): void
    {
        $method = new \ReflectionMethod(Paginator::class, '__construct');
        $params = $method->getParameters();

        $this->assertCount(1, $params);
        $this->assertSame('grid', $params[0]->getName());
        $this->assertSame('Dcat\Admin\Grid', $params[0]->getType()?->getName());
    }

    public function test_implements_renderable(): void
    {
        $interfaces = class_implements(Paginator::class);

        $this->assertArrayHasKey(Renderable::class, $interfaces);
    }

    public function test_render_method_signature(): void
    {
        $method = new \ReflectionMethod(Paginator::class, 'render');

        $this->assertTrue($method->isPublic());
        $this->assertSame(0, $method->getNumberOfParameters());
    }

    public function test_init_paginator_method_exists(): void
    {
        $method = new \ReflectionMethod(Paginator::class, 'initPaginator');

        $this->assertTrue($method->isProtected());
    }

    // -------------------------------------------------------------------------
    // Properties
    // -------------------------------------------------------------------------

    public function test_has_grid_property(): void
    {
        $property = new \ReflectionProperty(Paginator::class, 'grid');

        $this->assertTrue($property->isProtected());
    }

    public function test_has_paginator_property(): void
    {
        $property = new \ReflectionProperty(Paginator::class, 'paginator');

        $this->assertTrue($property->isPublic());
    }

    public function test_paginator_property_default_is_null(): void
    {
        $property = new \ReflectionProperty(Paginator::class, 'paginator');

        $this->assertNull($property->getDefaultValue());
    }

    // -------------------------------------------------------------------------
    // Methods
    // -------------------------------------------------------------------------

    public function test_pagination_links_method_exists(): void
    {
        $method = new \ReflectionMethod(Paginator::class, 'paginationLinks');

        $this->assertTrue($method->isProtected());
    }

    public function test_per_page_selector_method_exists(): void
    {
        $method = new \ReflectionMethod(Paginator::class, 'perPageSelector');

        $this->assertTrue($method->isProtected());
    }

    public function test_pagination_ranger_method_exists(): void
    {
        $method = new \ReflectionMethod(Paginator::class, 'paginationRanger');

        $this->assertTrue($method->isProtected());
    }
}
