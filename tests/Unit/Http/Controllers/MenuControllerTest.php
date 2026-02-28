<?php

namespace Dcat\Admin\Tests\Unit\Http\Controllers;

use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Http\Controllers\MenuController;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class MenuControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('admin.database.menu_model', \Dcat\Admin\Models\Menu::class);
    }

    public function test_controller_extends_admin_controller(): void
    {
        $controller = new MenuController;

        $this->assertInstanceOf(AdminController::class, $controller);
    }

    public function test_title_returns_translated_menu_string(): void
    {
        $controller = new MenuController;

        $result = $controller->title();

        $this->assertEquals(trans('admin.menu'), $result);
        $this->assertIsString($result);
    }

    public function test_icon_help_returns_fontawesome_link(): void
    {
        $controller = new MenuController;
        $reflection = new \ReflectionMethod($controller, 'iconHelp');
        $reflection->setAccessible(true);

        $result = $reflection->invoke($controller);

        $this->assertIsString($result);
        $this->assertStringContainsString('http://fontawesome.io/icons/', $result);
        $this->assertStringContainsString('<a href=', $result);
        $this->assertStringContainsString('target="_blank"', $result);
    }

    public function test_form_method_exists(): void
    {
        $this->assertTrue(method_exists(MenuController::class, 'form'));

        $reflection = new \ReflectionMethod(MenuController::class, 'form');
        $this->assertTrue($reflection->isPublic());
    }

    public function test_index_method_exists(): void
    {
        $this->assertTrue(method_exists(MenuController::class, 'index'));

        $reflection = new \ReflectionMethod(MenuController::class, 'index');
        $this->assertTrue($reflection->isPublic());
    }

    public function test_tree_view_method_exists_and_is_protected(): void
    {
        $this->assertTrue(method_exists(MenuController::class, 'treeView'));

        $reflection = new \ReflectionMethod(MenuController::class, 'treeView');
        $this->assertTrue($reflection->isProtected());
    }

    public function test_index_method_accepts_content_parameter(): void
    {
        $reflection = new \ReflectionMethod(MenuController::class, 'index');
        $parameters = $reflection->getParameters();

        $this->assertCount(1, $parameters);
        $this->assertEquals('content', $parameters[0]->getName());

        $type = $parameters[0]->getType();
        $this->assertNotNull($type);
        $this->assertEquals(\Dcat\Admin\Layout\Content::class, $type->getName());
    }
}
