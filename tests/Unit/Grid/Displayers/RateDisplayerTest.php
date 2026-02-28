<?php

namespace Dcat\Admin\Tests\Unit\Grid\Displayers;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Column;
use Dcat\Admin\Grid\Displayers\Rate;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class RateDisplayerTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function makeDisplayer($value): Rate
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('getName')->andReturn('test');

        $column = Mockery::mock(Column::class);
        $column->shouldReceive('getName')->andReturn('rate');

        $row = ['id' => 1, 'rate' => $value];

        return new Rate($value, $grid, $column, $row);
    }

    public function test_display_with_default_suffix(): void
    {
        $displayer = $this->makeDisplayer(85);
        $result = $displayer->display();

        $this->assertEquals('85%', $result);
    }

    public function test_display_with_custom_suffix(): void
    {
        $displayer = $this->makeDisplayer(75);
        $result = $displayer->display('‰');

        $this->assertEquals('75‰', $result);
    }

    public function test_display_with_decimals(): void
    {
        $displayer = $this->makeDisplayer(33.333);
        $result = $displayer->display('%', 2);

        $this->assertEquals('33.33%', $result);
    }

    public function test_display_null_value_returns_zero(): void
    {
        $displayer = $this->makeDisplayer(null);
        $result = $displayer->display();

        $this->assertEquals('0%', $result);
    }

    public function test_display_empty_string_returns_zero(): void
    {
        $displayer = $this->makeDisplayer('');
        $result = $displayer->display();

        $this->assertEquals('0%', $result);
    }

    public function test_display_without_decimals_preserves_original(): void
    {
        $displayer = $this->makeDisplayer(99.9);
        $result = $displayer->display('%', null);

        $this->assertEquals('99.9%', $result);
    }
}
