<?php

namespace Dcat\Admin\Tests\Unit\Http\Controllers;

use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Http\Controllers\DepartmentController;
use Dcat\Admin\Models\Department as DepartmentModel;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class DepartmentControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('admin.database.departments_model', DepartmentModel::class);
        $this->app['config']->set('admin.database.departments_table', 'admin_departments');
        $this->app['config']->set('admin.database.roles_model', \Dcat\Admin\Models\Role::class);
        $this->app['config']->set('admin.database.connection', 'testing');
    }

    public function test_controller_extends_admin_controller(): void
    {
        $controller = new DepartmentController;

        $this->assertInstanceOf(AdminController::class, $controller);
    }

    public function test_title_returns_translated_departments_string(): void
    {
        $controller = new DepartmentController;

        $result = $controller->title();

        $this->assertEquals(trans('admin.departments'), $result);
        $this->assertIsString($result);
    }

    public function test_title_method_is_public(): void
    {
        $reflection = new \ReflectionMethod(DepartmentController::class, 'title');

        $this->assertTrue($reflection->isPublic());
    }

    public function test_index_method_exists_and_is_public(): void
    {
        $this->assertTrue(method_exists(DepartmentController::class, 'index'));

        $reflection = new \ReflectionMethod(DepartmentController::class, 'index');
        $this->assertTrue($reflection->isPublic());
    }

    public function test_index_method_accepts_content_parameter(): void
    {
        $reflection = new \ReflectionMethod(DepartmentController::class, 'index');
        $parameters = $reflection->getParameters();

        $this->assertCount(1, $parameters);
        $this->assertEquals('content', $parameters[0]->getName());

        $type = $parameters[0]->getType();
        $this->assertNotNull($type);
        $this->assertEquals(\Dcat\Admin\Layout\Content::class, $type->getName());
    }

    public function test_tree_view_method_exists_and_is_protected(): void
    {
        $this->assertTrue(method_exists(DepartmentController::class, 'treeView'));

        $reflection = new \ReflectionMethod(DepartmentController::class, 'treeView');
        $this->assertTrue($reflection->isProtected());
    }

    public function test_form_method_exists_and_is_public(): void
    {
        $this->assertTrue(method_exists(DepartmentController::class, 'form'));

        $reflection = new \ReflectionMethod(DepartmentController::class, 'form');
        $this->assertTrue($reflection->isPublic());
    }
}
