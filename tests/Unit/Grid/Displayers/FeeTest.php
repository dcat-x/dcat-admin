<?php

namespace Dcat\Admin\Tests\Unit\Grid\Displayers;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Column;
use Dcat\Admin\Grid\Displayers\Fee;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class FeeTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function makeDisplayer($value): Fee
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('getName')->andReturn('test');

        $column = Mockery::mock(Column::class);
        $column->shouldReceive('getName')->andReturn('price');

        $row = ['id' => 1, 'price' => $value];

        return new Fee($value, $grid, $column, $row);
    }

    public function test_display_formats_cents_to_dollars(): void
    {
        $displayer = $this->makeDisplayer(10000);
        $result = $displayer->display();

        $this->assertEquals('$100.00', $result);
    }

    public function test_display_with_custom_symbol(): void
    {
        $displayer = $this->makeDisplayer(5099);
        $result = $displayer->display('¥');

        $this->assertEquals('¥50.99', $result);
    }

    public function test_display_with_custom_decimals(): void
    {
        $displayer = $this->makeDisplayer(12345);
        $result = $displayer->display('$', 3);

        $this->assertEquals('$123.450', $result);
    }

    public function test_display_zero_value(): void
    {
        $displayer = $this->makeDisplayer(0);
        $result = $displayer->display();

        $this->assertEquals('$0.00', $result);
    }

    public function test_display_null_value(): void
    {
        $displayer = $this->makeDisplayer(null);
        $result = $displayer->display();

        $this->assertEquals('$0.00', $result);
    }

    public function test_display_small_value(): void
    {
        $displayer = $this->makeDisplayer(1);
        $result = $displayer->display();

        $this->assertEquals('$0.01', $result);
    }
}
