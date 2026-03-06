<?php

namespace Dcat\Admin\Tests\Unit\Http\Controllers;

use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Http\Controllers\UserController;
use Dcat\Admin\Models\Administrator as AdministratorModel;
use Dcat\Admin\Tests\TestCase;

class UserControllerTest extends TestCase
{
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

        $this->assertSame(trans('admin.administrator'), $result);
        $this->assertIsString($result);
    }

    public function test_grid_returns_grid_instance(): void
    {
        $controller = new class extends UserController
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
        $controller = new UserController;
        $this->assertInstanceOf(\Dcat\Admin\Form::class, $controller->form());
    }

    public function test_destroy_delegates_to_form_destroy_for_non_default_user_id(): void
    {
        $controller = new class extends UserController
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

    public function test_controller_references_default_id_constant(): void
    {
        $this->assertSame(1, AdministratorModel::DEFAULT_ID);
    }

    public function test_index_and_show_are_callable(): void
    {
        $controller = new UserController;

        $this->assertTrue(is_callable([$controller, 'index']));
        $this->assertTrue(is_callable([$controller, 'show']));
    }

    public function test_grid_and_form_can_be_exposed_from_subclass(): void
    {
        $controller = new class extends UserController
        {
            public function exposeGrid(): \Dcat\Admin\Grid
            {
                return $this->grid();
            }

            public function exposeForm(): \Dcat\Admin\Form
            {
                return $this->form();
            }
        };

        $this->assertInstanceOf(\Dcat\Admin\Grid::class, $controller->exposeGrid());
        $this->assertInstanceOf(\Dcat\Admin\Form::class, $controller->exposeForm());
    }
}
