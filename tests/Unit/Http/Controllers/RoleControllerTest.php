<?php

declare(strict_types=1);

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

        $this->assertSame(trans('admin.roles'), $result);
        $this->assertIsString($result);
    }

    public function test_grid_returns_grid_instance(): void
    {
        $controller = new class extends RoleController
        {
            public function exposeGrid(): \Dcat\Admin\Grid
            {
                return $this->grid();
            }
        };

        $this->assertInstanceOf(\Dcat\Admin\Grid::class, $controller->exposeGrid());
    }

    public function test_form_returns_form_instance(): void
    {
        $controller = new RoleController;
        $this->assertInstanceOf(\Dcat\Admin\Form::class, $controller->form());
    }

    public function test_destroy_delegates_to_form_destroy_for_non_admin_role_id(): void
    {
        $controller = new class extends RoleController
        {
            public ?int $destroyedId = null;

            public function form()
            {
                return new class($this)
                {
                    public function __construct(private object $controller) {}

                    public function destroy($id): string
                    {
                        $this->controller->destroyedId = (int) $id;

                        return 'destroyed-'.$id;
                    }
                };
            }
        };

        $result = $controller->destroy(2);

        $this->assertSame('destroyed-2', $result);
        $this->assertSame(2, $controller->destroyedId);
    }
}
