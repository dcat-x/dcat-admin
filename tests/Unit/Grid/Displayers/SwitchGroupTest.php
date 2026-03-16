<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Displayers;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Column;
use Dcat\Admin\Grid\Displayers\SwitchGroup;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class SwitchGroupTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function makeDisplayer($value): SwitchGroup
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('getName')->andReturn('test');
        $grid->shouldReceive('getKeyName')->andReturn('id');
        $grid->shouldReceive('resource')->andReturn('/admin/users');

        $column = Mockery::mock(Column::class);
        $column->shouldReceive('getName')->andReturn('switches');

        $row = ['id' => 1, 'is_active' => 1, 'is_admin' => 0];

        return new SwitchGroup($value, $grid, $column, $row);
    }

    public function test_display_with_indexed_columns_renders_string(): void
    {
        $displayer = $this->makeDisplayer(null);
        $result = $displayer->display(['is_active', 'is_admin']);

        $this->assertIsString($result);
    }

    public function test_display_with_associative_columns_renders_string(): void
    {
        $displayer = $this->makeDisplayer(null);
        $result = $displayer->display(['is_active' => 'Active', 'is_admin' => 'Admin']);

        $this->assertIsString($result);
    }

    public function test_get_key_returns_row_key(): void
    {
        $displayer = $this->makeDisplayer(null);
        $key = $displayer->getKey();

        $this->assertSame(1, $key);
    }

    public function test_resource_returns_grid_resource(): void
    {
        $displayer = $this->makeDisplayer(null);
        $resource = $displayer->resource();

        $this->assertSame('/admin/users', $resource);
    }

    public function test_color_sets_color_property(): void
    {
        $displayer = $this->makeDisplayer(null);
        $displayer->color('primary');

        $ref = new \ReflectionProperty($displayer, 'color');
        $ref->setAccessible(true);
        $color = $ref->getValue($displayer);

        $this->assertNotNull($color);
    }
}
