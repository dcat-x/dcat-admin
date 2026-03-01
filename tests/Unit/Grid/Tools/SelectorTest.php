<?php

namespace Dcat\Admin\Tests\Unit\Grid\Tools;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Tools\Selector;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Support\Collection;
use Mockery;

class SelectorTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function makeGrid(): Grid
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('makeName')->andReturnUsing(function ($suffix) {
            return 'grid'.$suffix;
        });

        return $grid;
    }

    protected function makeSelector(): Selector
    {
        return new Selector($this->makeGrid());
    }

    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    // -------------------------------------------------------------------------
    // Constructor
    // -------------------------------------------------------------------------

    public function test_constructor_accepts_grid(): void
    {
        $grid = $this->makeGrid();
        $selector = new Selector($grid);

        $this->assertSame($grid, $this->getProtectedProperty($selector, 'grid'));
    }

    public function test_constructor_creates_collection(): void
    {
        $selector = $this->makeSelector();

        $selectors = $this->getProtectedProperty($selector, 'selectors');

        $this->assertInstanceOf(Collection::class, $selectors);
        $this->assertTrue($selectors->isEmpty());
    }

    // -------------------------------------------------------------------------
    // select / selectOne
    // -------------------------------------------------------------------------

    public function test_select_adds_selector(): void
    {
        $selector = $this->makeSelector();

        $selector->select('status', 'Status', ['active' => 'Active', 'inactive' => 'Inactive']);

        $all = $selector->all();
        $this->assertCount(1, $all);
        $this->assertArrayHasKey('status', $all->toArray());
        $this->assertSame('Status', $all['status']['label']);
        $this->assertSame('many', $all['status']['type']);
    }

    public function test_select_one_adds_one_type_selector(): void
    {
        $selector = $this->makeSelector();

        $selector->selectOne('category', 'Category', ['a' => 'A', 'b' => 'B']);

        $all = $selector->all();
        $this->assertSame('one', $all['category']['type']);
    }

    public function test_select_returns_self(): void
    {
        $selector = $this->makeSelector();

        $result = $selector->select('col', 'Label', []);

        $this->assertSame($selector, $result);
    }

    public function test_select_with_array_label(): void
    {
        $selector = $this->makeSelector();

        $selector->select('status', ['active' => 'Active', 'inactive' => 'Inactive']);

        $all = $selector->all();
        $this->assertArrayHasKey('status', $all->toArray());
        $this->assertSame(['active' => 'Active', 'inactive' => 'Inactive'], $all['status']['options']);
    }

    public function test_select_with_array_label_and_closure_query(): void
    {
        $selector = $this->makeSelector();

        $query = function () {};
        $selector->select('status', ['active' => 'Active'], $query);

        $all = $selector->all();
        $this->assertSame($query, $all['status']['query']);
    }

    // -------------------------------------------------------------------------
    // all()
    // -------------------------------------------------------------------------

    public function test_all_returns_selectors(): void
    {
        $selector = $this->makeSelector();

        $selector->select('col1', 'Label1', []);
        $selector->select('col2', 'Label2', []);

        $this->assertCount(2, $selector->all());
    }

    public function test_all_with_format_key(): void
    {
        $selector = $this->makeSelector();

        $selector->select('user.status', 'Status', []);

        $formatted = $selector->all(true);

        $this->assertArrayHasKey('user_status', $formatted->toArray());
        $this->assertArrayNotHasKey('user.status', $formatted->toArray());
    }

    // -------------------------------------------------------------------------
    // formatKey
    // -------------------------------------------------------------------------

    public function test_format_key_replaces_dot_with_underscore(): void
    {
        $selector = $this->makeSelector();

        $this->assertSame('user_status', $selector->formatKey('user.status'));
        $this->assertSame('a_b_c', $selector->formatKey('a.b.c'));
        $this->assertSame('simple', $selector->formatKey('simple'));
    }

    // -------------------------------------------------------------------------
    // parseSelected
    // -------------------------------------------------------------------------

    public function test_parse_selected_returns_array(): void
    {
        $selector = $this->makeSelector();

        $result = $selector->parseSelected();

        $this->assertIsArray($result);
    }

    // -------------------------------------------------------------------------
    // getQueryName
    // -------------------------------------------------------------------------

    public function test_get_query_name_uses_grid_make_name(): void
    {
        $selector = $this->makeSelector();

        $this->assertSame('grid_selector', $selector->getQueryName());
    }
}
