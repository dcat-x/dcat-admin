<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Tools;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\BatchAction;
use Dcat\Admin\Grid\GridAction;
use Dcat\Admin\Grid\Tools\ActionDivider;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class ActionDividerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    public function test_can_be_instantiated(): void
    {
        $divider = new ActionDivider;

        $this->assertInstanceOf(ActionDivider::class, $divider);
    }

    public function test_extends_batch_action(): void
    {
        $divider = new ActionDivider;

        $this->assertInstanceOf(BatchAction::class, $divider);
    }

    public function test_extends_grid_action(): void
    {
        $divider = new ActionDivider;

        $this->assertInstanceOf(GridAction::class, $divider);
    }

    public function test_render_returns_empty_string(): void
    {
        $divider = new ActionDivider;

        $this->assertSame('', $divider->render());
    }

    public function test_render_returns_empty_string_after_set_grid(): void
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('resource')->andReturn('/admin/users');
        $grid->shouldReceive('getName')->andReturn('test-grid');

        $divider = new ActionDivider;
        $divider->setGrid($grid);

        $this->assertSame('', $divider->render());
    }

    public function test_set_grid_stores_grid_as_parent(): void
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('resource')->andReturn('/admin/users');
        $grid->shouldReceive('getName')->andReturn('');

        $divider = new ActionDivider;
        $result = $divider->setGrid($grid);

        $this->assertSame($grid, $this->getProtectedProperty($divider, 'parent'));
        $this->assertSame($divider, $result);
    }

    public function test_resource_delegates_to_grid(): void
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('resource')->andReturn('/admin/categories');
        $grid->shouldReceive('getName')->andReturn('');

        $divider = new ActionDivider;
        $divider->setGrid($grid);

        $this->assertSame('/admin/categories', $divider->resource());
    }
}
