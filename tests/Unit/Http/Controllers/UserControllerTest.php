<?php

namespace Dcat\Admin\Tests\Unit\Http\Controllers;

use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Http\Controllers\UserController;
use Dcat\Admin\Models\Administrator as AdministratorModel;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class UserControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('admin.database.users_model', AdministratorModel::class);
        $this->app['config']->set('admin.database.users_table', 'admin_users');
        $this->app['config']->set('admin.database.roles_model', \Dcat\Admin\Models\Role::class);
        $this->app['config']->set('admin.database.permissions_model', \Dcat\Admin\Models\Permission::class);
        $this->app['config']->set('admin.database.menu_model', \Dcat\Admin\Models\Menu::class);
        $this->app['config']->set('admin.database.connection', 'testing');
        $this->app['config']->set('admin.permission.enable', true);
        $this->app['config']->set('admin.department.enable', false);
    }

    public function test_controller_extends_admin_controller(): void
    {
        $controller = new UserController;

        $this->assertInstanceOf(AdminController::class, $controller);
    }

    public function test_title_returns_translated_administrator_string(): void
    {
        $controller = new UserController;

        $result = $controller->title();

        $this->assertEquals(trans('admin.administrator'), $result);
        $this->assertIsString($result);
    }

    public function test_title_method_is_public(): void
    {
        $reflection = new \ReflectionMethod(UserController::class, 'title');
        $this->assertTrue($reflection->isPublic());
    }

    public function test_grid_method_exists_and_is_protected(): void
    {
        $this->assertTrue(method_exists(UserController::class, 'grid'));

        $reflection = new \ReflectionMethod(UserController::class, 'grid');
        $this->assertTrue($reflection->isProtected());
    }

    public function test_detail_method_exists_and_is_protected(): void
    {
        $this->assertTrue(method_exists(UserController::class, 'detail'));

        $reflection = new \ReflectionMethod(UserController::class, 'detail');
        $this->assertTrue($reflection->isProtected());
    }

    public function test_detail_method_accepts_id_parameter(): void
    {
        $reflection = new \ReflectionMethod(UserController::class, 'detail');
        $parameters = $reflection->getParameters();

        $this->assertCount(1, $parameters);
        $this->assertEquals('id', $parameters[0]->getName());
    }

    public function test_form_method_is_public(): void
    {
        $reflection = new \ReflectionMethod(UserController::class, 'form');
        $this->assertTrue($reflection->isPublic());
    }

    public function test_destroy_method_is_public(): void
    {
        $reflection = new \ReflectionMethod(UserController::class, 'destroy');
        $this->assertTrue($reflection->isPublic());
    }

    public function test_destroy_method_overrides_parent(): void
    {
        $reflection = new \ReflectionMethod(UserController::class, 'destroy');
        $this->assertEquals(UserController::class, $reflection->getDeclaringClass()->getName());
    }

    public function test_destroy_method_accepts_id_parameter(): void
    {
        $reflection = new \ReflectionMethod(UserController::class, 'destroy');
        $parameters = $reflection->getParameters();

        $this->assertCount(1, $parameters);
        $this->assertEquals('id', $parameters[0]->getName());
    }

    public function test_index_method_inherited_from_admin_controller(): void
    {
        $reflection = new \ReflectionMethod(UserController::class, 'index');
        $this->assertEquals(AdminController::class, $reflection->getDeclaringClass()->getName());
    }

    public function test_show_method_inherited_from_admin_controller(): void
    {
        $reflection = new \ReflectionMethod(UserController::class, 'show');
        $this->assertEquals(AdminController::class, $reflection->getDeclaringClass()->getName());
    }

    public function test_controller_references_default_id_constant(): void
    {
        $this->assertEquals(1, AdministratorModel::DEFAULT_ID);
    }

    public function test_grid_method_declared_in_user_controller(): void
    {
        $reflection = new \ReflectionMethod(UserController::class, 'grid');
        $this->assertEquals(UserController::class, $reflection->getDeclaringClass()->getName());
    }

    public function test_form_method_declared_in_user_controller(): void
    {
        $reflection = new \ReflectionMethod(UserController::class, 'form');
        $this->assertEquals(UserController::class, $reflection->getDeclaringClass()->getName());
    }
}
