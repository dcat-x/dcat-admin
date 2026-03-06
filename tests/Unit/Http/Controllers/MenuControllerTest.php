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

        $this->assertSame(trans('admin.menu'), $result);
        $this->assertIsString($result);
    }

    public function test_icon_help_returns_fontawesome_link(): void
    {
        $controller = new class extends MenuController
        {
            public function exposeIconHelp(): string
            {
                return $this->iconHelp();
            }
        };

        $result = $controller->exposeIconHelp();

        $this->assertIsString($result);
        $this->assertStringContainsString('http://fontawesome.io/icons/', $result);
        $this->assertStringContainsString('<a href=', $result);
        $this->assertStringContainsString('target="_blank"', $result);
    }

    public function test_form_returns_form_instance(): void
    {
        $controller = new MenuController;

        $this->assertInstanceOf(\Dcat\Admin\Form::class, $controller->form());
    }

    public function test_index_builds_content_with_row_callback_body(): void
    {
        $content = Mockery::mock(\Dcat\Admin\Layout\Content::class);
        $content->shouldReceive('title')->once()->andReturnSelf();
        $content->shouldReceive('description')->once()->andReturnSelf();
        $content->shouldReceive('body')->once()->with(Mockery::type(\Closure::class))->andReturnSelf();

        $controller = new MenuController;
        $result = $controller->index($content);

        $this->assertSame($content, $result);
    }

    public function test_tree_view_returns_tree_instance(): void
    {
        $controller = new class extends MenuController
        {
            public function exposeTreeView(): \Dcat\Admin\Tree
            {
                return $this->treeView();
            }
        };

        $this->assertInstanceOf(\Dcat\Admin\Tree::class, $controller->exposeTreeView());
    }
}
