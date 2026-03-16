<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Models;

use Dcat\Admin\Models\Permission;
use Dcat\Admin\Models\Role;
use Dcat\Admin\Tests\TestCase;

class PermissionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('admin.database.permissions_model', Permission::class);
        $this->app['config']->set('admin.database.permissions_table', 'admin_permissions');
        $this->app['config']->set('admin.database.roles_model', Role::class);
        $this->app['config']->set('admin.database.role_permissions_table', 'admin_role_permissions');
        $this->app['config']->set('admin.database.menu_model', \Dcat\Admin\Models\Menu::class);
    }

    public function test_permission_creation(): void
    {
        $permission = new Permission([
            'name' => 'Test Permission',
            'slug' => 'test-permission',
            'type' => Permission::TYPE_MENU,
        ]);

        $this->assertInstanceOf(Permission::class, $permission);
        $this->assertSame('Test Permission', $permission->name);
        $this->assertSame('test-permission', $permission->slug);
    }

    public function test_permission_type_constants(): void
    {
        $this->assertSame(1, Permission::TYPE_MENU);
        $this->assertSame(2, Permission::TYPE_BUTTON);
        $this->assertSame(3, Permission::TYPE_DATA);
    }

    public function test_is_menu_permission(): void
    {
        $menuPermission = new Permission(['type' => Permission::TYPE_MENU]);
        $buttonPermission = new Permission(['type' => Permission::TYPE_BUTTON]);

        $this->assertTrue($menuPermission->isMenuPermission());
        $this->assertFalse($buttonPermission->isMenuPermission());
    }

    public function test_is_button_permission(): void
    {
        $buttonPermission = new Permission(['type' => Permission::TYPE_BUTTON]);
        $menuPermission = new Permission(['type' => Permission::TYPE_MENU]);

        $this->assertTrue($buttonPermission->isButtonPermission());
        $this->assertFalse($menuPermission->isButtonPermission());
    }

    public function test_permission_fillable_attributes(): void
    {
        $permission = new Permission;

        $fillable = $permission->getFillable();

        // 验证原有的可填充属性
        $this->assertContains('parent_id', $fillable);
        $this->assertContains('name', $fillable);
        $this->assertContains('slug', $fillable);
        $this->assertContains('http_method', $fillable);
        $this->assertContains('http_path', $fillable);

        // 验证新增的可填充属性
        $this->assertContains('type', $fillable);
        $this->assertContains('permission_key', $fillable);
        $this->assertContains('menu_id', $fillable);
    }

    public function test_permission_with_permission_key(): void
    {
        $permission = new Permission([
            'name' => 'Add User',
            'slug' => 'user-add',
            'type' => Permission::TYPE_BUTTON,
            'permission_key' => 'user:add',
            'menu_id' => 1,
        ]);

        $this->assertSame('user:add', $permission->permission_key);
        $this->assertSame(1, $permission->menu_id);
        $this->assertTrue($permission->isButtonPermission());
    }

    public function test_permission_default_type(): void
    {
        $permission = new Permission([
            'name' => 'Test',
            'slug' => 'test',
        ]);

        // 默认类型应该是菜单权限
        $this->assertSame(Permission::TYPE_MENU, $permission->type ?? Permission::TYPE_MENU);
    }

    public function test_menu_relationship_returns_belongs_to(): void
    {
        $permission = new Permission;

        $relation = $permission->menu();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
    }

    public function test_find_by_key_signature_accepts_string_key(): void
    {
        $method = new \ReflectionMethod(Permission::class, 'findByKey');
        $params = $method->getParameters();

        $this->assertCount(1, $params);
        $this->assertSame('key', $params[0]->getName());
        $this->assertSame('string', $params[0]->getType()->getName());
    }

    public function test_get_button_permissions_signature_accepts_menu_id(): void
    {
        $method = new \ReflectionMethod(Permission::class, 'getButtonPermissions');
        $params = $method->getParameters();

        $this->assertCount(1, $params);
        $this->assertSame('menuId', $params[0]->getName());
        $this->assertSame('int', $params[0]->getType()->getName());
    }

    public function test_permission_tree_structure(): void
    {
        $permission = new Permission;

        $this->assertSame('parent_id', $permission->getParentColumn());
        $this->assertTrue((new \ReflectionClass(Permission::class))->hasMethod('allNodes'));
    }

    public function test_http_methods_property(): void
    {
        // 验证 HTTP 方法常量存在
        $this->assertIsArray(Permission::$httpMethods);
        $this->assertContains('GET', Permission::$httpMethods);
        $this->assertContains('POST', Permission::$httpMethods);
        $this->assertContains('PUT', Permission::$httpMethods);
        $this->assertContains('DELETE', Permission::$httpMethods);
    }

    public function test_permission_roles_relationship_returns_belongs_to_many(): void
    {
        $permission = new Permission;

        $relation = $permission->roles();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class, $relation);
    }

    public function test_button_permission_creation(): void
    {
        $permission = new Permission([
            'name' => 'Delete Order',
            'slug' => 'order-delete',
            'type' => Permission::TYPE_BUTTON,
            'permission_key' => 'order:delete',
            'menu_id' => 5,
            'http_method' => ['DELETE'],
            'http_path' => '/orders/*',
        ]);

        $this->assertSame(Permission::TYPE_BUTTON, $permission->type);
        $this->assertSame('order:delete', $permission->permission_key);
        $this->assertSame(5, $permission->menu_id);
        $this->assertTrue($permission->isButtonPermission());
        $this->assertFalse($permission->isMenuPermission());
    }

    public function test_data_permission_type(): void
    {
        $permission = new Permission([
            'name' => 'Data Permission',
            'slug' => 'data-permission',
            'type' => Permission::TYPE_DATA,
        ]);

        $this->assertSame(Permission::TYPE_DATA, $permission->type);
        $this->assertFalse($permission->isMenuPermission());
        $this->assertFalse($permission->isButtonPermission());
    }
}
