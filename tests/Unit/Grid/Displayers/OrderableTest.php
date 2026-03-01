<?php

namespace Dcat\Admin\Tests\Unit\Grid\Displayers;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Column;
use Dcat\Admin\Grid\Displayers\AbstractDisplayer;
use Dcat\Admin\Grid\Displayers\Orderable;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Support\Fluent;
use Mockery;

class OrderableTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_orderable_extends_abstract_displayer(): void
    {
        $this->assertTrue(is_subclass_of(Orderable::class, AbstractDisplayer::class));
    }

    public function test_display_returns_html_with_arrows(): void
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('getKeyName')->andReturn('id');
        $grid->shouldReceive('getRowName')->andReturn('grid-row');
        $grid->shouldReceive('resource')->andReturn('/admin/items');

        $column = Mockery::mock(Column::class);
        $column->shouldReceive('getName')->andReturn('order');

        $row = new Fluent(['id' => 5]);

        $displayer = new Orderable('1', $grid, $column, $row);
        $html = $displayer->display();

        $this->assertStringContainsString('data-id="5"', $html);
        $this->assertStringContainsString('data-direction="1"', $html);
        $this->assertStringContainsString('data-direction="0"', $html);
        $this->assertStringContainsString('grid-row-orderable', $html);
    }

    public function test_display_contains_svg_icons(): void
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('getKeyName')->andReturn('id');
        $grid->shouldReceive('getRowName')->andReturn('grid-row');
        $grid->shouldReceive('resource')->andReturn('/admin/items');

        $column = Mockery::mock(Column::class);
        $column->shouldReceive('getName')->andReturn('order');

        $row = new Fluent(['id' => 1]);

        $displayer = new Orderable('1', $grid, $column, $row);
        $html = $displayer->display();

        $this->assertStringContainsString('<svg', $html);
        $this->assertStringContainsString('</svg>', $html);
    }
}
