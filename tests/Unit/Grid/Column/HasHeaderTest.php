<?php

namespace Dcat\Admin\Tests\Unit\Grid\Column;

use Dcat\Admin\Grid\Column;
use Dcat\Admin\Grid\Column\HasHeader;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class HasHeaderTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_trait_exists(): void
    {
        $this->assertTrue(trait_exists(HasHeader::class));
    }

    public function test_filter_property_is_public(): void
    {
        $ref = new \ReflectionProperty(HasHeader::class, 'filter');
        $this->assertTrue($ref->isPublic());
    }

    public function test_headers_property_is_protected(): void
    {
        $ref = new \ReflectionProperty(HasHeader::class, 'headers');
        $this->assertTrue($ref->isProtected());
    }

    public function test_headers_default_empty_array(): void
    {
        $ref = new \ReflectionProperty(HasHeader::class, 'headers');
        $ref->setAccessible(true);
        $this->assertSame([], $ref->getDefaultValue());
    }

    public function test_has_add_header_method(): void
    {
        $this->assertTrue(method_exists(Column::class, 'addHeader'));
    }

    public function test_has_sortable_method(): void
    {
        $this->assertTrue(method_exists(Column::class, 'sortable'));
    }

    public function test_has_filter_method_via_trait(): void
    {
        $this->assertTrue(method_exists(Column::class, 'filter'));
    }

    public function test_has_filter_by_value_method(): void
    {
        $this->assertTrue(method_exists(Column::class, 'filterByValue'));
    }

    public function test_has_help_method(): void
    {
        $this->assertTrue(method_exists(Column::class, 'help'));
    }

    public function test_has_bind_filter_query_method(): void
    {
        $this->assertTrue(method_exists(Column::class, 'bindFilterQuery'));
    }

    public function test_has_render_header_method(): void
    {
        $this->assertTrue(method_exists(Column::class, 'renderHeader'));
    }
}
