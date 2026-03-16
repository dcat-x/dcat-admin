<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Http\Controllers;

use Dcat\Admin\Form;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Http\Controllers\DepartmentController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Models\Department as DepartmentModel;
use Dcat\Admin\Models\Role;
use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Tree;
use Mockery;

class DepartmentControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('admin.database.departments_model', DepartmentModel::class);
        $this->app['config']->set('admin.database.departments_table', 'admin_departments');
        $this->app['config']->set('admin.database.roles_model', Role::class);
        $this->app['config']->set('admin.database.connection', 'testing');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_controller_extends_admin_controller(): void
    {
        $this->assertInstanceOf(AdminController::class, new DepartmentController);
    }

    public function test_title_returns_departments_translation(): void
    {
        $controller = new DepartmentController;

        $this->assertSame(trans('admin.departments'), $controller->title());
    }

    public function test_index_builds_content_with_row_callback_body(): void
    {
        $content = Mockery::mock(Content::class);
        $content->shouldReceive('title')->once()->andReturnSelf();
        $content->shouldReceive('description')->once()->andReturnSelf();
        $content->shouldReceive('body')->once()->with(Mockery::type(\Closure::class))->andReturnSelf();

        $controller = new DepartmentController;
        $result = $controller->index($content);

        $this->assertSame($content, $result);
    }

    public function test_tree_view_returns_tree_instance(): void
    {
        $controller = new class extends DepartmentController
        {
            public function exposeTreeView(): Tree
            {
                return $this->treeView();
            }
        };

        $this->assertInstanceOf(Tree::class, $controller->exposeTreeView());
    }

    public function test_form_returns_form_instance(): void
    {
        $controller = new DepartmentController;

        $this->assertInstanceOf(Form::class, $controller->form());
    }
}
