<?php

namespace Dcat\Admin\Tests\Unit\Grid\Displayers;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Column;
use Dcat\Admin\Grid\Displayers\EmptyData;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class EmptyDataTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function makeDisplayer($value): EmptyData
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('getName')->andReturn('test');

        $column = Mockery::mock(Column::class);
        $column->shouldReceive('getName')->andReturn('description');

        $row = ['id' => 1, 'description' => $value];

        return new EmptyData($value, $grid, $column, $row);
    }

    public function test_display_returns_value_when_not_empty(): void
    {
        $displayer = $this->makeDisplayer('Hello World');
        $result = $displayer->display();

        $this->assertEquals('Hello World', $result);
    }

    public function test_display_returns_default_placeholder_for_null(): void
    {
        $displayer = $this->makeDisplayer(null);
        $result = $displayer->display();

        $this->assertEquals('-', $result);
    }

    public function test_display_returns_default_placeholder_for_empty_string(): void
    {
        $displayer = $this->makeDisplayer('');
        $result = $displayer->display();

        $this->assertEquals('-', $result);
    }

    public function test_display_returns_custom_placeholder_for_empty(): void
    {
        $displayer = $this->makeDisplayer('');
        $result = $displayer->display('N/A');

        $this->assertEquals('N/A', $result);
    }

    public function test_display_returns_custom_placeholder_for_null(): void
    {
        $displayer = $this->makeDisplayer(null);
        $result = $displayer->display('--');

        $this->assertEquals('--', $result);
    }

    public function test_display_returns_numeric_value(): void
    {
        $displayer = $this->makeDisplayer(42);
        $result = $displayer->display();

        $this->assertEquals(42, $result);
    }
}
