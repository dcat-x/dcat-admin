<?php

namespace Dcat\Admin\Tests\Unit\Models;

use Dcat\Admin\Models\Role;
use Dcat\Admin\Tests\TestCase;

class RoleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('admin.database.roles_model', Role::class);
        $this->app['config']->set('admin.database.roles_table', 'admin_roles');
    }

    public function test_role_creation(): void
    {
        $role = new Role([
            'name' => 'Test Role',
            'slug' => 'test-role',
        ]);

        $this->assertInstanceOf(Role::class, $role);
        $this->assertEquals('Test Role', $role->name);
        $this->assertEquals('test-role', $role->slug);
    }

    public function test_role_constants(): void
    {
        $this->assertEquals('administrator', Role::ADMINISTRATOR);
        $this->assertEquals(1, Role::ADMINISTRATOR_ID);
    }

    public function test_is_administrator_static(): void
    {
        $this->assertTrue(Role::isAdministrator('administrator'));
        $this->assertFalse(Role::isAdministrator('manager'));
        $this->assertFalse(Role::isAdministrator(null));
    }

    public function test_role_fillable_attributes(): void
    {
        $role = new Role;

        $fillable = $role->getFillable();

        $this->assertContains('name', $fillable);
        $this->assertContains('slug', $fillable);
    }

    public function test_administrators_relationship_exists(): void
    {
        $role = new Role;

        $this->assertTrue(method_exists($role, 'administrators'));
    }

    public function test_permissions_relationship_exists(): void
    {
        $role = new Role;

        $this->assertTrue(method_exists($role, 'permissions'));
    }

    public function test_menus_relationship_exists(): void
    {
        $role = new Role;

        $this->assertTrue(method_exists($role, 'menus'));
    }

    public function test_departments_relationship_exists(): void
    {
        $role = new Role;

        // 验证新增的部门关系存在
        $this->assertTrue(method_exists($role, 'departments'));
    }

    public function test_data_rules_relationship_exists(): void
    {
        $role = new Role;

        // 验证新增的数据规则关系存在
        $this->assertTrue(method_exists($role, 'dataRules'));
    }

    public function test_can_method_exists(): void
    {
        $role = new Role;

        $this->assertTrue(method_exists($role, 'can'));
    }

    public function test_cannot_method_exists(): void
    {
        $role = new Role;

        $this->assertTrue(method_exists($role, 'cannot'));
    }

    public function test_get_permission_id_method_exists(): void
    {
        $this->assertTrue(method_exists(Role::class, 'getPermissionId'));
    }

    public function test_administrator_role(): void
    {
        $adminRole = new Role([
            'name' => 'Administrator',
            'slug' => Role::ADMINISTRATOR,
        ]);

        $this->assertEquals(Role::ADMINISTRATOR, $adminRole->slug);
        $this->assertTrue(Role::isAdministrator($adminRole->slug));
    }

    public function test_regular_role(): void
    {
        $managerRole = new Role([
            'name' => 'Manager',
            'slug' => 'manager',
        ]);

        $this->assertEquals('manager', $managerRole->slug);
        $this->assertFalse(Role::isAdministrator($managerRole->slug));
    }

    public function test_role_with_departments(): void
    {
        $role = new Role([
            'name' => 'Department Manager',
            'slug' => 'department-manager',
        ]);

        // 验证部门关系方法返回 BelongsToMany
        $relation = $role->departments();

        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\BelongsToMany::class,
            $relation
        );
    }

    public function test_role_with_data_rules(): void
    {
        $role = new Role([
            'name' => 'Data Manager',
            'slug' => 'data-manager',
        ]);

        // 验证数据规则关系方法返回 BelongsToMany
        $relation = $role->dataRules();

        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\BelongsToMany::class,
            $relation
        );
    }
}
