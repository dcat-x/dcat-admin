<?php

namespace Dcat\Admin\Tests\Unit\Grid\Filter\Layout;

use Dcat\Admin\Grid\Filter\AbstractFilter;
use Dcat\Admin\Grid\Filter\Layout\Column;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Support\Collection;
use Mockery;

class ColumnTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_constructor_default_width_12(): void
    {
        $column = new Column;

        $this->assertSame(12, $column->width());
    }

    public function test_constructor_custom_width(): void
    {
        $column = new Column(6);

        $this->assertSame(6, $column->width());
    }

    public function test_filters_returns_collection(): void
    {
        $column = new Column;

        $this->assertInstanceOf(Collection::class, $column->filters());
    }

    public function test_filters_initially_empty(): void
    {
        $column = new Column;

        $this->assertTrue($column->filters()->isEmpty());
    }

    public function test_add_filter_adds_to_collection(): void
    {
        $column = new Column;

        $filter = Mockery::mock(AbstractFilter::class);
        $column->addFilter($filter);

        $this->assertCount(1, $column->filters());
        $this->assertSame($filter, $column->filters()->first());
    }

    public function test_width_getter_returns_width(): void
    {
        $column = new Column(8);

        $this->assertSame(8, $column->width());
    }

    public function test_width_setter_sets_width(): void
    {
        $column = new Column(12);

        $column->width(4);

        $this->assertSame(4, $column->width());
    }

    public function test_remove_filter_by_id(): void
    {
        $column = new Column;

        $filter1 = Mockery::mock(AbstractFilter::class);
        $filter1->shouldReceive('getId')->andReturn('name');

        $filter2 = Mockery::mock(AbstractFilter::class);
        $filter2->shouldReceive('getId')->andReturn('email');

        $column->addFilter($filter1);
        $column->addFilter($filter2);

        $this->assertCount(2, $column->filters());

        $column->removeFilterByID('name');

        $this->assertCount(1, $column->filters());
        $this->assertSame('email', $column->filters()->first()->getId());
    }
}
