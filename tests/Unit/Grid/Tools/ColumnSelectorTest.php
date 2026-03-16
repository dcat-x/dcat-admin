<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Tools;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Column;
use Dcat\Admin\Grid\Tools\ColumnSelector;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Support\Collection;
use Mockery;

class ColumnSelectorTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function createMockGrid(array $columns = []): Grid
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('getName')->andReturn('test');

        // Build column collection
        $columnCollection = new Collection;
        foreach ($columns as $name => $label) {
            $col = Mockery::mock(Column::class);
            $col->shouldReceive('getName')->andReturn($name);
            $col->shouldReceive('getLabel')->andReturn($label);
            $columnCollection->put($name, $col);
        }

        $grid->shouldReceive('columns')->andReturn($columnCollection);
        $grid->shouldReceive('getComplexHeaders')->andReturn(null);
        $grid->shouldReceive('getComplexHeaderNames')->andReturn([]);
        $grid->shouldReceive('getColumnNames')->andReturn(array_keys($columns));
        $grid->shouldReceive('getVisibleColumnsFromQuery')->andReturn([]);
        $grid->shouldReceive('getColumnSelectorQueryName')->andReturn('_columns_');

        return $grid;
    }

    public function test_constructor_stores_grid(): void
    {
        $grid = $this->createMockGrid();
        $selector = new ColumnSelector($grid);

        $ref = new \ReflectionProperty($selector, 'grid');
        $ref->setAccessible(true);

        $this->assertSame($grid, $ref->getValue($selector));
    }

    public function test_select_column_name_constant(): void
    {
        $this->assertSame('_columns_', ColumnSelector::SELECT_COLUMN_NAME);
    }

    public function test_default_ignored_columns_include_system_columns(): void
    {
        $grid = $this->createMockGrid();
        $selector = new ColumnSelector($grid);

        $ref = new \ReflectionProperty($selector, 'ignoredColumns');
        $ref->setAccessible(true);
        $ignored = $ref->getValue($selector);

        $this->assertContains(Column::SELECT_COLUMN_NAME, $ignored);
        $this->assertContains(Column::ACTION_COLUMN_NAME, $ignored);
    }

    public function test_ignore_adds_single_column(): void
    {
        $grid = $this->createMockGrid();
        $selector = new ColumnSelector($grid);

        $result = $selector->ignore('secret_field');

        $this->assertSame($selector, $result);

        $ref = new \ReflectionProperty($selector, 'ignoredColumns');
        $ref->setAccessible(true);
        $ignored = $ref->getValue($selector);

        $this->assertContains('secret_field', $ignored);
    }

    public function test_ignore_adds_array_of_columns(): void
    {
        $grid = $this->createMockGrid();
        $selector = new ColumnSelector($grid);

        $selector->ignore(['field_a', 'field_b']);

        $ref = new \ReflectionProperty($selector, 'ignoredColumns');
        $ref->setAccessible(true);
        $ignored = $ref->getValue($selector);

        $this->assertContains('field_a', $ignored);
        $this->assertContains('field_b', $ignored);
    }

    public function test_is_column_ignored_returns_true_for_system_columns(): void
    {
        $grid = $this->createMockGrid();
        $selector = new ColumnSelector($grid);

        $method = new \ReflectionMethod($selector, 'isColumnIgnored');
        $method->setAccessible(true);

        $this->assertTrue($method->invoke($selector, Column::SELECT_COLUMN_NAME));
        $this->assertTrue($method->invoke($selector, Column::ACTION_COLUMN_NAME));
    }

    public function test_is_column_ignored_returns_false_for_normal_columns(): void
    {
        $grid = $this->createMockGrid();
        $selector = new ColumnSelector($grid);

        $method = new \ReflectionMethod($selector, 'isColumnIgnored');
        $method->setAccessible(true);

        $this->assertFalse($method->invoke($selector, 'name'));
        $this->assertFalse($method->invoke($selector, 'email'));
    }

    public function test_is_column_ignored_returns_true_after_ignore(): void
    {
        $grid = $this->createMockGrid();
        $selector = new ColumnSelector($grid);
        $selector->ignore('password');

        $method = new \ReflectionMethod($selector, 'isColumnIgnored');
        $method->setAccessible(true);

        $this->assertTrue($method->invoke($selector, 'password'));
    }

    public function test_get_grid_columns_excludes_system_columns(): void
    {
        $columns = [
            Column::SELECT_COLUMN_NAME => '',
            'name' => 'Name',
            'email' => 'Email',
            Column::ACTION_COLUMN_NAME => '',
        ];

        $grid = $this->createMockGrid($columns);
        $selector = new ColumnSelector($grid);

        $method = new \ReflectionMethod($selector, 'getGridColumns');
        $method->setAccessible(true);
        $result = $method->invoke($selector);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertTrue($result->has('name'));
        $this->assertTrue($result->has('email'));
        $this->assertArrayNotHasKey(Column::SELECT_COLUMN_NAME, $result->toArray());
        $this->assertArrayNotHasKey(Column::ACTION_COLUMN_NAME, $result->toArray());
    }

    public function test_get_visible_column_names_returns_all_non_system(): void
    {
        $columns = [
            'name' => 'Name',
            'email' => 'Email',
            'phone' => 'Phone',
        ];

        $grid = $this->createMockGrid($columns);
        $selector = new ColumnSelector($grid);

        $method = new \ReflectionMethod($selector, 'getVisibleColumnNames');
        $method->setAccessible(true);
        $result = $method->invoke($selector);

        $this->assertContains('name', $result);
        $this->assertContains('email', $result);
        $this->assertContains('phone', $result);
    }

    public function test_ignore_returns_this_for_chaining(): void
    {
        $grid = $this->createMockGrid();
        $selector = new ColumnSelector($grid);

        $result = $selector->ignore('field1');

        $this->assertSame($selector, $result);
    }
}
