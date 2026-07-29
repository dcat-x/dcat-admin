<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Http\Controllers;

use Dcat\Admin\Form;
use Dcat\Admin\Form\Events\Saved;
use Dcat\Admin\Http\Controllers\MenuController;
use Dcat\Admin\Http\Controllers\PermissionController;
use Dcat\Admin\Http\Controllers\RoleController;
use Dcat\Admin\Models\Menu;
use Dcat\Admin\Models\Permission;
use Dcat\Admin\Models\Role;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ControllerClosureBindingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('admin.database.connection', 'testing');
        $this->app['config']->set('admin.database.menu_model', Menu::class);
        $this->app['config']->set('admin.database.permissions_model', Permission::class);
        $this->app['config']->set('admin.database.permissions_table', 'admin_permissions');
        $this->app['config']->set('admin.database.roles_model', Role::class);
        $this->app['config']->set('admin.database.roles_table', 'admin_roles');
        $this->app['config']->set('admin.database.role_permissions_table', 'admin_role_permissions');
        $this->app['config']->set('admin.permission.enable', true);
        $this->app['config']->set('admin.menu.bind_permission', true);
    }

    public function test_menu_form_renders_controller_owned_options_callbacks(): void
    {
        $controller = new class extends MenuController
        {
            public array $resolved = [];

            protected function getMenuSelectOptions(): array
            {
                $this->resolved[] = 'menus';

                return [0 => 'Root'];
            }

            protected function getRoleOptions(): array
            {
                $this->resolved[] = 'roles';

                return [1 => 'Administrator'];
            }

            protected function getPermissionNodes(): array
            {
                $this->resolved[] = 'permissions';

                return [];
            }
        };

        $form = $controller->form();
        $form->model(new Menu);
        $build = new \ReflectionMethod($form, 'build');
        $build->invoke($form);
        $html = collect(['parent_id', 'roles', 'permissions'])
            ->map(fn (string $column) => $form->field($column)->render())
            ->implode('');

        $this->assertIsString($html);
        $this->assertSame(['menus', 'roles', 'permissions'], $controller->resolved);
    }

    public function test_menu_saved_callback_keeps_controller_context(): void
    {
        $controller = new class extends MenuController
        {
            public array $reportedEvents = [];

            protected function reportConfigHealthIssues(string $event): void
            {
                $this->reportedEvents[] = $event;
            }
        };
        $form = $controller->form();
        $form->model(new Menu);

        $this->dispatchSavedEvent($form);

        $this->assertSame(['menu.saved'], $controller->reportedEvents);
    }

    public function test_permission_saved_callback_keeps_controller_context(): void
    {
        $controller = new class extends PermissionController
        {
            public array $reportedEvents = [];

            protected function reportConfigHealthIssues(string $event): void
            {
                $this->reportedEvents[] = $event;
            }
        };
        $form = $controller->form();
        $form->model(new Permission);

        $this->dispatchSavedEvent($form);

        $this->assertSame(['permission.saved'], $controller->reportedEvents);
    }

    public function test_role_detail_renders_controller_owned_permission_nodes_callback(): void
    {
        $schema = Schema::connection('testing');
        $schema->create('admin_roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug');
            $table->string('name');
            $table->timestamps();
        });
        $schema->create('admin_permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('parent_id')->default(0);
            $table->string('name');
            $table->string('slug');
            $table->timestamps();
        });
        $schema->create('admin_role_permissions', function (Blueprint $table) {
            $table->unsignedInteger('role_id');
            $table->unsignedInteger('permission_id');
            $table->timestamps();
        });

        try {
            DB::connection('testing')->table('admin_roles')->insert([
                'id' => 2,
                'slug' => 'operator',
                'name' => 'Operator',
            ]);

            $controller = new class extends RoleController
            {
                public function exposeDetail(int $id)
                {
                    return $this->detail($id);
                }

                protected function getPermissionNodes(): array
                {
                    return [];
                }
            };

            $html = $controller->exposeDetail(2)->render();

            $this->assertIsString($html);
        } finally {
            $schema->dropIfExists('admin_role_permissions');
            $schema->dropIfExists('admin_permissions');
            $schema->dropIfExists('admin_roles');
        }
    }

    private function dispatchSavedEvent(Form $form): void
    {
        foreach ($this->app['events']->getListeners(Saved::class) as $listener) {
            $listener(Saved::class, [new Saved($form, [true])]);
        }
    }
}
