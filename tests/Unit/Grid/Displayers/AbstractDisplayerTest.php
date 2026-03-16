<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Displayers;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Column;
use Dcat\Admin\Grid\Displayers\AbstractDisplayer;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Support\Fluent;
use Mockery;

class AbstractDisplayerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function createConcreteDisplayer($value, Grid $grid, Column $column, $row): AbstractDisplayer
    {
        return new class($value, $grid, $column, $row) extends AbstractDisplayer
        {
            public function display()
            {
                return $this->value;
            }
        };
    }

    protected function createMockGridAndColumn(): array
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('getKeyName')->andReturn('id');
        $grid->shouldReceive('resource')->andReturn('/admin/users');

        $column = Mockery::mock(Column::class);
        $column->shouldReceive('getName')->andReturn('name');

        return [$grid, $column];
    }

    public function test_constructor_sets_properties(): void
    {
        [$grid, $column] = $this->createMockGridAndColumn();
        $row = ['id' => 1, 'name' => 'Test'];

        $displayer = $this->createConcreteDisplayer('test value', $grid, $column, $row);

        $this->assertInstanceOf(AbstractDisplayer::class, $displayer);
        $this->assertInstanceOf(Fluent::class, $displayer->row);
    }

    public function test_set_row_converts_array_to_fluent(): void
    {
        [$grid, $column] = $this->createMockGridAndColumn();
        $row = ['id' => 1, 'name' => 'Test'];

        $displayer = $this->createConcreteDisplayer('test', $grid, $column, $row);

        $this->assertInstanceOf(Fluent::class, $displayer->row);
        $this->assertSame(1, $displayer->row->id);
    }

    public function test_get_key(): void
    {
        [$grid, $column] = $this->createMockGridAndColumn();
        $row = new Fluent(['id' => 42, 'name' => 'Test']);

        $displayer = $this->createConcreteDisplayer('test', $grid, $column, $row);

        $this->assertSame(42, $displayer->getKey());
    }

    public function test_resource(): void
    {
        [$grid, $column] = $this->createMockGridAndColumn();
        $row = ['id' => 1];

        $displayer = $this->createConcreteDisplayer('test', $grid, $column, $row);

        $this->assertSame('/admin/users', $displayer->resource());
    }

    public function test_display_returns_value(): void
    {
        [$grid, $column] = $this->createMockGridAndColumn();
        $row = ['id' => 1];

        $displayer = $this->createConcreteDisplayer('Hello', $grid, $column, $row);

        $this->assertSame('Hello', $displayer->display());
    }

    public function test_get_element_name_simple(): void
    {
        [$grid, $column] = $this->createMockGridAndColumn();
        $row = ['id' => 1];

        $displayer = $this->createConcreteDisplayer('test', $grid, $column, $row);

        $this->assertSame('name', $displayer->getElementName());
    }

    public function test_get_element_name_nested(): void
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('getKeyName')->andReturn('id');

        $column = Mockery::mock(Column::class);
        $column->shouldReceive('getName')->andReturn('address.city');

        $row = ['id' => 1];
        $displayer = $this->createConcreteDisplayer('test', $grid, $column, $row);

        $this->assertSame('address[city]', $displayer->getElementName());
    }
}
