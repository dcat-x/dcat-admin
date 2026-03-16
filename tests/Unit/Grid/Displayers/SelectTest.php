<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Displayers;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Column;
use Dcat\Admin\Grid\Displayers\Select;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class SelectTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function makeDisplayer($value): Select
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('getName')->andReturn('test');
        $grid->shouldReceive('getKeyName')->andReturn('id');
        $grid->shouldReceive('resource')->andReturn('/admin/users');

        $column = Mockery::mock(Column::class);
        $column->shouldReceive('getName')->andReturn('status');

        $row = ['id' => 5, 'status' => $value];

        return new Select($value, $grid, $column, $row);
    }

    public function test_url_combines_resource_and_key(): void
    {
        $displayer = $this->makeDisplayer('active');

        $method = new \ReflectionMethod($displayer, 'url');
        $method->setAccessible(true);

        $url = $method->invoke($displayer);

        $this->assertSame('/admin/users/5', $url);
    }

    public function test_constructor_stores_value(): void
    {
        $displayer = $this->makeDisplayer('pending');

        $ref = new \ReflectionProperty($displayer, 'value');
        $ref->setAccessible(true);

        $this->assertSame('pending', $ref->getValue($displayer));
    }

    public function test_constructor_stores_column(): void
    {
        $displayer = $this->makeDisplayer('active');

        $ref = new \ReflectionProperty($displayer, 'column');
        $ref->setAccessible(true);

        $column = $ref->getValue($displayer);
        $this->assertSame('status', $column->getName());
    }

    public function test_constructor_stores_grid(): void
    {
        $displayer = $this->makeDisplayer('active');

        $ref = new \ReflectionProperty($displayer, 'grid');
        $ref->setAccessible(true);

        $grid = $ref->getValue($displayer);
        $this->assertSame('/admin/users', $grid->resource());
    }

    public function test_get_key_returns_row_key(): void
    {
        $displayer = $this->makeDisplayer('active');

        $this->assertSame(5, $displayer->getKey());
    }

    public function test_resource_returns_grid_resource(): void
    {
        $displayer = $this->makeDisplayer('active');

        $this->assertSame('/admin/users', $displayer->resource());
    }

    public function test_get_element_name_returns_column_name(): void
    {
        $displayer = $this->makeDisplayer('active');

        $this->assertSame('status', $displayer->getElementName());
    }
}
