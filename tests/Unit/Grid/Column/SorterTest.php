<?php

namespace Dcat\Admin\Tests\Unit\Grid\Column;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Column\Sorter;
use Dcat\Admin\Grid\Model;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Contracts\Support\Renderable;
use Mockery;

class SorterTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function createSorter(string $columnName = 'name', $cast = null): Sorter
    {
        $model = Mockery::mock(Model::class);
        $model->shouldReceive('getSortName')->andReturn('_sort');

        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('model')->andReturn($model);

        return new Sorter($grid, $columnName, $cast);
    }

    public function test_constructor_stores_grid(): void
    {
        $model = Mockery::mock(Model::class);
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('model')->andReturn($model);

        $sorter = new Sorter($grid, 'col', null);

        $ref = new \ReflectionProperty(Sorter::class, 'grid');
        $ref->setAccessible(true);
        $this->assertSame($grid, $ref->getValue($sorter));
    }

    public function test_constructor_stores_column_name(): void
    {
        $sorter = $this->createSorter('my_column');

        $ref = new \ReflectionProperty(Sorter::class, 'columnName');
        $ref->setAccessible(true);
        $this->assertSame('my_column', $ref->getValue($sorter));
    }

    public function test_constructor_stores_cast(): void
    {
        $sorter = $this->createSorter('name', 'integer');

        $ref = new \ReflectionProperty(Sorter::class, 'cast');
        $ref->setAccessible(true);
        $this->assertSame('integer', $ref->getValue($sorter));
    }

    public function test_implements_renderable(): void
    {
        $sorter = $this->createSorter();
        $this->assertInstanceOf(Renderable::class, $sorter);
    }

    public function test_render_returns_string(): void
    {
        $sorter = $this->createSorter('title');
        $html = $sorter->render();

        $this->assertIsString($html);
    }

    public function test_render_contains_sort_class(): void
    {
        $sorter = $this->createSorter('title');
        $html = $sorter->render();

        $this->assertStringContainsString('grid-sort', $html);
    }

    public function test_render_contains_column_name_in_url(): void
    {
        $sorter = $this->createSorter('price');
        $html = $sorter->render();

        $this->assertStringContainsString('price', $html);
    }

    public function test_constructor_accepts_null_cast(): void
    {
        $sorter = $this->createSorter('name', null);

        $ref = new \ReflectionProperty(Sorter::class, 'cast');
        $ref->setAccessible(true);
        $this->assertNull($ref->getValue($sorter));
    }
}
