<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Tools;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\BatchAction;
use Dcat\Admin\Grid\GridAction;
use Dcat\Admin\Grid\Tools\BatchDelete;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class BatchDeleteTest extends TestCase
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

    protected function createBatchDeleteWithGrid(string $title = 'Delete', string $resource = '/admin/users', string $gridName = 'test-grid'): BatchDelete
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('resource')->andReturn($resource);
        $grid->shouldReceive('getName')->andReturn($gridName);

        $action = new BatchDelete($title);
        $action->setGrid($grid);

        return $action;
    }

    // -------------------------------------------------------------------------
    // Constructor
    // -------------------------------------------------------------------------

    public function test_constructor_sets_title(): void
    {
        $action = new BatchDelete('Delete Selected');

        $this->assertSame('Delete Selected', $this->getProtectedProperty($action, 'title'));
    }

    public function test_extends_batch_action(): void
    {
        $action = new BatchDelete('Delete');

        $this->assertInstanceOf(BatchAction::class, $action);
    }

    public function test_extends_grid_action(): void
    {
        $action = new BatchDelete('Delete');

        $this->assertInstanceOf(GridAction::class, $action);
    }

    // -------------------------------------------------------------------------
    // setGrid / resource
    // -------------------------------------------------------------------------

    public function test_set_grid_stores_grid_as_parent(): void
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('resource')->andReturn('/admin/users');
        $grid->shouldReceive('getName')->andReturn('');

        $action = new BatchDelete('Delete');
        $result = $action->setGrid($grid);

        $this->assertSame($grid, $this->getProtectedProperty($action, 'parent'));
        $this->assertSame($action, $result); // returns $this
    }

    public function test_resource_returns_grid_resource(): void
    {
        $action = $this->createBatchDeleteWithGrid('Delete', '/admin/articles');

        $this->assertSame('/admin/articles', $action->resource());
    }

    // -------------------------------------------------------------------------
    // render()
    // -------------------------------------------------------------------------

    public function test_render_contains_batch_delete_data_action(): void
    {
        $action = $this->createBatchDeleteWithGrid('Delete Items');
        $html = $action->render();

        $this->assertStringContainsString('data-action="batch-delete"', $html);
    }

    public function test_render_contains_title(): void
    {
        $action = $this->createBatchDeleteWithGrid('Remove All');
        $html = $action->render();

        $this->assertStringContainsString('Remove All', $html);
    }

    public function test_render_contains_resource_url(): void
    {
        $action = $this->createBatchDeleteWithGrid('Delete', '/admin/posts');
        $html = $action->render();

        $this->assertStringContainsString('/admin/posts', $html);
    }

    public function test_render_contains_trash_icon(): void
    {
        $action = $this->createBatchDeleteWithGrid();
        $html = $action->render();

        $this->assertStringContainsString('icon-trash', $html);
    }

    public function test_render_contains_data_name_attribute(): void
    {
        $action = $this->createBatchDeleteWithGrid('Delete', '/admin/users', 'my-grid');
        $html = $action->render();

        $this->assertStringContainsString('data-name="my-grid"', $html);
    }

    public function test_render_contains_redirect_url(): void
    {
        $action = $this->createBatchDeleteWithGrid();
        $html = $action->render();

        $this->assertStringContainsString('data-redirect=', $html);
    }

    // -------------------------------------------------------------------------
    // getSelectedKeysScript (inherited from BatchAction)
    // -------------------------------------------------------------------------

    public function test_get_selected_keys_script_contains_grid_name(): void
    {
        $action = $this->createBatchDeleteWithGrid('Delete', '/admin/users', 'users-grid');

        $script = $action->getSelectedKeysScript();

        $this->assertStringContainsString('users-grid', $script);
        $this->assertStringContainsString('Dcat.grid.selected', $script);
    }
}
