<?php

namespace Dcat\Admin\Tests\Unit\Http\Controllers;

use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Http\Controllers\PermissionController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class PermissionControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('admin.database.permissions_model', \Dcat\Admin\Models\Permission::class);
        $this->app['config']->set('admin.database.permissions_table', 'admin_permissions');
        $this->app['config']->set('admin.database.menu_model', \Dcat\Admin\Models\Menu::class);
        $this->app['config']->set('admin.database.connection', 'testing');
        $this->app['config']->set('admin.menu.permission_bind_menu', true);
    }

    public function test_controller_extends_admin_controller(): void
    {
        $controller = new PermissionController;

        $this->assertInstanceOf(AdminController::class, $controller);
    }

    public function test_title_returns_translated_permissions_string(): void
    {
        $controller = new PermissionController;
        $method = new \ReflectionMethod($controller, 'title');
        $method->setAccessible(true);

        $result = $method->invoke($controller);

        $this->assertEquals(trans('admin.permissions'), $result);
        $this->assertIsString($result);
    }

    public function test_index_method_is_public(): void
    {
        $reflection = new \ReflectionMethod(PermissionController::class, 'index');
        $this->assertTrue($reflection->isPublic());
    }

    public function test_index_method_accepts_content_parameter(): void
    {
        $reflection = new \ReflectionMethod(PermissionController::class, 'index');
        $parameters = $reflection->getParameters();

        $this->assertCount(1, $parameters);
        $this->assertEquals('content', $parameters[0]->getName());
        $this->assertEquals(Content::class, $parameters[0]->getType()->getName());
    }

    public function test_form_method_is_public(): void
    {
        $reflection = new \ReflectionMethod(PermissionController::class, 'form');
        $this->assertTrue($reflection->isPublic());
    }

    public function test_tree_view_method_exists_and_is_protected(): void
    {
        $this->assertTrue(method_exists(PermissionController::class, 'treeView'));

        $reflection = new \ReflectionMethod(PermissionController::class, 'treeView');
        $this->assertTrue($reflection->isProtected());
    }

    public function test_get_routes_method_is_public(): void
    {
        $reflection = new \ReflectionMethod(PermissionController::class, 'getRoutes');
        $this->assertTrue($reflection->isPublic());
    }

    public function test_get_routes_returns_array(): void
    {
        $controller = new PermissionController;
        $result = $controller->getRoutes();

        $this->assertIsArray($result);
    }

    public function test_get_http_methods_options_is_protected(): void
    {
        $reflection = new \ReflectionMethod(PermissionController::class, 'getHttpMethodsOptions');
        $this->assertTrue($reflection->isProtected());
    }

    public function test_get_http_methods_options_returns_array(): void
    {
        $controller = new PermissionController;
        $method = new \ReflectionMethod($controller, 'getHttpMethodsOptions');
        $method->setAccessible(true);

        $result = $method->invoke($controller);

        $this->assertIsArray($result);
        // HTTP methods should include standard methods
        $this->assertNotEmpty($result);
    }

    public function test_get_http_methods_keys_match_values(): void
    {
        $controller = new PermissionController;
        $method = new \ReflectionMethod($controller, 'getHttpMethodsOptions');
        $method->setAccessible(true);

        $result = $method->invoke($controller);

        // array_combine means keys === values
        foreach ($result as $key => $value) {
            $this->assertEquals($key, $value);
        }
    }

    public function test_controller_does_not_have_grid_method(): void
    {
        // PermissionController uses treeView for index, not grid
        $reflection = new \ReflectionClass(PermissionController::class);
        $methods = $reflection->getMethods();

        $ownGridMethod = array_filter($methods, function ($m) {
            return $m->getName() === 'grid' && $m->getDeclaringClass()->getName() === PermissionController::class;
        });

        $this->assertEmpty($ownGridMethod);
    }

    public function test_controller_does_not_have_detail_method(): void
    {
        // PermissionController does not define its own detail method
        $reflection = new \ReflectionClass(PermissionController::class);
        $methods = $reflection->getMethods();

        $ownDetailMethod = array_filter($methods, function ($m) {
            return $m->getName() === 'detail' && $m->getDeclaringClass()->getName() === PermissionController::class;
        });

        $this->assertEmpty($ownDetailMethod);
    }
}
