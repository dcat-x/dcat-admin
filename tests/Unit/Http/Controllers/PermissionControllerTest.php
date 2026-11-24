<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Http\Controllers;

use Dcat\Admin\Form;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Http\Controllers\PermissionController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Models\Menu;
use Dcat\Admin\Models\Permission;
use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Tree;
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

        $this->app['config']->set('admin.database.permissions_model', Permission::class);
        $this->app['config']->set('admin.database.permissions_table', 'admin_permissions');
        $this->app['config']->set('admin.database.menu_model', Menu::class);
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
        $controller = new class extends PermissionController
        {
            public function exposeTitle(): string
            {
                return $this->title();
            }
        };

        $result = $controller->exposeTitle();

        $this->assertSame(trans('admin.permissions'), $result);
        $this->assertIsString($result);
    }

    public function test_index_builds_content_with_tree_body(): void
    {
        $tree = Mockery::mock(Tree::class);

        $controller = new class($tree) extends PermissionController
        {
            public function __construct(private Tree $tree) {}

            protected function treeView()
            {
                return $this->tree;
            }
        };

        $content = Mockery::mock(Content::class);
        $content->shouldReceive('title')->once()->andReturnSelf();
        $content->shouldReceive('description')->once()->andReturnSelf();
        $content->shouldReceive('body')->once()->with($tree)->andReturnSelf();

        $result = $controller->index($content);
        $this->assertSame($content, $result);
    }

    public function test_tree_view_returns_tree_instance(): void
    {
        $controller = new class extends PermissionController
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
        $controller = new PermissionController;
        $this->assertInstanceOf(Form::class, $controller->form());
    }

    public function test_button_permission_fields_are_only_required_for_button_type(): void
    {
        $this->app['config']->set('admin.menu.permission_bind_menu', false);
        $this->app['config']->set('admin.database.menu_model', PermissionControllerTestMenuStub::class);

        $controller = new class extends PermissionController
        {
            protected function getPermissionSelectOptions(): array
            {
                return [0 => 'Root'];
            }
        };
        $form = $controller->form();
        (new \ReflectionMethod($form, 'build'))->invoke($form);

        foreach (['menu_id', 'permission_key'] as $column) {
            $field = $form->field($column);

            $this->assertNotNull($field, $column);
            // Hidden cascade fields must not carry the HTML "required" attribute,
            // otherwise browser validation silently blocks submission for other types.
            $this->assertNull($field->getAttribute('required'), $column);

            $validator = $field->getValidator(['type' => 1, $column => null]);
            $this->assertFalse($validator !== false && $validator->fails(), $column.' should be optional for menu permission');

            $validator = $field->getValidator(['type' => 2, $column => null]);
            $this->assertTrue($validator !== false && $validator->fails(), $column.' should be required for button permission');
        }
    }

    public function test_get_routes_returns_array(): void
    {
        $controller = new PermissionController;
        $result = $controller->getRoutes();

        $this->assertIsArray($result);
    }

    public function test_get_http_methods_options_returns_array(): void
    {
        $controller = new class extends PermissionController
        {
            public function exposeHttpMethodsOptions(): array
            {
                return $this->getHttpMethodsOptions();
            }
        };

        $result = $controller->exposeHttpMethodsOptions();

        $this->assertIsArray($result);
        // HTTP methods should include standard methods
        $this->assertNotEmpty($result);
    }

    public function test_get_http_methods_keys_match_values(): void
    {
        $controller = new class extends PermissionController
        {
            public function exposeHttpMethodsOptions(): array
            {
                return $this->getHttpMethodsOptions();
            }
        };

        $result = $controller->exposeHttpMethodsOptions();

        // array_combine means keys === values
        foreach ($result as $key => $value) {
            $this->assertSame($key, $value);
        }
    }
}

class PermissionControllerTestMenuStub
{
    public static function selectOptions(): array
    {
        return [1 => 'Index'];
    }
}
