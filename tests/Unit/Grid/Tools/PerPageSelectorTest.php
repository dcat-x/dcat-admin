<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Tools;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Model;
use Dcat\Admin\Grid\Tools\PerPageSelector;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Contracts\Support\Renderable;
use Mockery;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

#[AllowMockObjectsWithoutExpectations]
class PerPageSelectorTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function createMockGrid(int $perPage = 20, array $perPages = [10, 20, 30, 50, 100]): Grid
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('getName')->andReturn('test');
        $grid->shouldReceive('getPerPage')->andReturn($perPage);
        $grid->shouldReceive('getPerPages')->andReturn($perPages);
        $grid->shouldReceive('getPerPageName')->andReturn('grid-per-page');

        $model = Mockery::mock(Model::class);
        $model->shouldReceive('getPerPageName')->andReturn('grid-per-page');

        $grid->shouldReceive('model')->andReturn($model);
        $grid->shouldReceive('makeName')->andReturnUsing(function ($key) {
            return 'grid-'.$key;
        });

        return $grid;
    }

    public function test_constructor_stores_grid(): void
    {
        $grid = $this->createMockGrid();
        $selector = new PerPageSelector($grid);

        $ref = new \ReflectionProperty($selector, 'parent');
        $ref->setAccessible(true);

        $this->assertSame($grid, $ref->getValue($selector));
    }

    public function test_initialize_sets_per_page_name(): void
    {
        $grid = $this->createMockGrid();
        $selector = new PerPageSelector($grid);

        $ref = new \ReflectionProperty($selector, 'perPageName');
        $ref->setAccessible(true);

        $this->assertSame('grid-per-page', $ref->getValue($selector));
    }

    public function test_initialize_sets_default_per_page(): void
    {
        $grid = $this->createMockGrid(20);
        $selector = new PerPageSelector($grid);

        $ref = new \ReflectionProperty($selector, 'perPage');
        $ref->setAccessible(true);

        $this->assertSame(20, $ref->getValue($selector));
    }

    public function test_get_options_returns_sorted_unique_collection(): void
    {
        $grid = $this->createMockGrid(20, [10, 20, 30, 50, 100]);
        $selector = new PerPageSelector($grid);

        $options = $selector->getOptions();

        $this->assertCount(5, $options);
        $values = $options->values()->toArray();
        $this->assertSame([10, 20, 30, 50, 100], $values);
    }

    public function test_get_options_includes_current_per_page(): void
    {
        $grid = $this->createMockGrid(25, [10, 20, 50]);
        $selector = new PerPageSelector($grid);

        $options = $selector->getOptions();

        $this->assertTrue($options->contains(25));
    }

    public function test_get_options_deduplicates_values(): void
    {
        $grid = $this->createMockGrid(20, [10, 20, 30]);
        $selector = new PerPageSelector($grid);

        $options = $selector->getOptions();

        // 20 appears in both perPages and perPage, should only appear once
        $count = $options->filter(fn ($v) => $v === 20)->count();
        $this->assertSame(1, $count);
    }

    public function test_get_options_sorts_ascending(): void
    {
        $grid = $this->createMockGrid(15, [50, 10, 30]);
        $selector = new PerPageSelector($grid);

        $options = $selector->getOptions();
        $values = $options->values()->toArray();

        $sorted = $values;
        sort($sorted);

        $this->assertSame($sorted, $values);
    }

    public function test_implements_renderable(): void
    {
        $grid = $this->createMockGrid();
        $selector = new PerPageSelector($grid);

        $this->assertInstanceOf(Renderable::class, $selector);
    }
}
