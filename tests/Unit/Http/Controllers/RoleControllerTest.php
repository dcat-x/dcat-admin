<?php

namespace Dcat\Admin\Tests\Unit\Http\Controllers;

use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Http\Controllers\RoleController;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class RoleControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('admin.database.roles_model', \Dcat\Admin\Models\Role::class);
        $this->app['config']->set('admin.database.roles_table', 'admin_roles');
        $this->app['config']->set('admin.database.permissions_model', \Dcat\Admin\Models\Permission::class);
        $this->app['config']->set('admin.database.menu_model', \Dcat\Admin\Models\Menu::class);
        $this->app['config']->set('admin.database.connection', 'testing');
        $this->app['config']->set('admin.menu.role_bind_menu', true);
    }

    public function test_controller_extends_admin_controller(): void
    {
        $controller = new RoleController;

        $this->assertInstanceOf(AdminController::class, $controller);
    }

    public function test_title_returns_translated_roles_string(): void
    {
        $controller = new RoleController;

        $result = $controller->title();

        $this->assertEquals(trans('admin.roles'), $result);
        $this->assertIsString($result);
    }

    public function test_title_method_is_public(): void
    {
        $reflection = new \ReflectionMethod(RoleController::class, 'title');
        $this->assertTrue($reflection->isPublic());
    }

    public function test_grid_method_exists_and_is_protected(): void
    {
        $this->assertTrue(method_exists(RoleController::class, 'grid'));

        $reflection = new \ReflectionMethod(RoleController::class, 'grid');
        $this->assertTrue($reflection->isProtected());
    }

    public function test_detail_method_exists_and_is_protected(): void
    {
        $this->assertTrue(method_exists(RoleController::class, 'detail'));

        $reflection = new \ReflectionMethod(RoleController::class, 'detail');
        $this->assertTrue($reflection->isProtected());
    }

    public function test_detail_method_accepts_id_parameter(): void
    {
        $reflection = new \ReflectionMethod(RoleController::class, 'detail');
        $parameters = $reflection->getParameters();

        $this->assertCount(1, $parameters);
        $this->assertEquals('id', $parameters[0]->getName());
    }

    public function test_form_method_is_public(): void
    {
        $reflection = new \ReflectionMethod(RoleController::class, 'form');
        $this->assertTrue($reflection->isPublic());
    }

    public function test_destroy_method_is_public(): void
    {
        $reflection = new \ReflectionMethod(RoleController::class, 'destroy');
        $this->assertTrue($reflection->isPublic());
    }

    public function test_destroy_method_accepts_id_parameter(): void
    {
        $reflection = new \ReflectionMethod(RoleController::class, 'destroy');
        $parameters = $reflection->getParameters();

        $this->assertCount(1, $parameters);
        $this->assertEquals('id', $parameters[0]->getName());
    }

    public function test_index_method_inherited_from_admin_controller(): void
    {
        $reflection = new \ReflectionMethod(RoleController::class, 'index');
        $this->assertEquals(AdminController::class, $reflection->getDeclaringClass()->getName());
    }

    public function test_show_method_inherited_from_admin_controller(): void
    {
        $reflection = new \ReflectionMethod(RoleController::class, 'show');
        $this->assertEquals(AdminController::class, $reflection->getDeclaringClass()->getName());
    }

    public function test_edit_method_inherited_from_admin_controller(): void
    {
        $reflection = new \ReflectionMethod(RoleController::class, 'edit');
        $this->assertEquals(AdminController::class, $reflection->getDeclaringClass()->getName());
    }
}
