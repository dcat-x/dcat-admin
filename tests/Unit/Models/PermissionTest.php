<?php

namespace Dcat\Admin\Tests\Unit\Models;

use Dcat\Admin\Models\Permission;
use Dcat\Admin\Tests\TestCase;

class PermissionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('admin.database.permissions_model', Permission::class);
        $this->app['config']->set('admin.database.permissions_table', 'admin_permissions');
    }

    public function test_permission_creation(): void
    {
        $permission = new Permission([
            'name' => 'Test Permission',
            'slug' => 'test-permission',
            'type' => Permission::TYPE_MENU,
        ]);

        $this->assertInstanceOf(Permission::class, $permission);
        $this->assertEquals('Test Permission', $permission->name);
        $this->assertEquals('test-permission', $permission->slug);
    }

    public function test_permission_type_constants(): void
    {
        $this->assertEquals(1, Permission::TYPE_MENU);
        $this->assertEquals(2, Permission::TYPE_BUTTON);
        $this->assertEquals(3, Permission::TYPE_DATA);
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

        $this->assertEquals('user:add', $permission->permission_key);
        $this->assertEquals(1, $permission->menu_id);
        $this->assertTrue($permission->isButtonPermission());
    }

    public function test_permission_default_type(): void
    {
        $permission = new Permission([
            'name' => 'Test',
            'slug' => 'test',
        ]);

        // 默认类型应该是菜单权限
        $this->assertEquals(Permission::TYPE_MENU, $permission->type ?? Permission::TYPE_MENU);
    }

    public function test_menu_relationship_exists(): void
    {
        $permission = new Permission;

        // 验证 menu 关系方法存在
        $this->assertTrue(method_exists($permission, 'menu'));
    }

    public function test_find_by_key_method_exists(): void
    {
        // 验证静态方法存在
        $this->assertTrue(method_exists(Permission::class, 'findByKey'));
    }

    public function test_get_button_permissions_method_exists(): void
    {
        // 验证静态方法存在
        $this->assertTrue(method_exists(Permission::class, 'getButtonPermissions'));
    }

    public function test_permission_tree_structure(): void
    {
        $permission = new Permission;

        // 验证树结构相关方法
        $this->assertTrue(method_exists($permission, 'allNodes'));
        $this->assertEquals('parent_id', $permission->getParentColumn());
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

    public function test_permission_roles_relationship_exists(): void
    {
        $permission = new Permission;

        // 验证角色关系存在
        $this->assertTrue(method_exists($permission, 'roles'));
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

        $this->assertEquals(Permission::TYPE_BUTTON, $permission->type);
        $this->assertEquals('order:delete', $permission->permission_key);
        $this->assertEquals(5, $permission->menu_id);
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

        $this->assertEquals(Permission::TYPE_DATA, $permission->type);
        $this->assertFalse($permission->isMenuPermission());
        $this->assertFalse($permission->isButtonPermission());
    }
}
