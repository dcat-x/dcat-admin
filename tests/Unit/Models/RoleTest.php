<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Models;

use Dcat\Admin\Models\Role;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class RoleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('admin.database.roles_model', Role::class);
        $this->app['config']->set('admin.database.roles_table', 'admin_roles');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_role_creation(): void
    {
        $role = new Role([
            'name' => 'Test Role',
            'slug' => 'test-role',
        ]);

        $this->assertInstanceOf(Role::class, $role);
        $this->assertSame('Test Role', $role->name);
        $this->assertSame('test-role', $role->slug);
    }

    public function test_role_constants(): void
    {
        $this->assertSame('administrator', Role::ADMINISTRATOR);
        $this->assertSame(1, Role::ADMINISTRATOR_ID);
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

    public function test_administrators_relationship_returns_belongs_to_many(): void
    {
        $method = new \ReflectionMethod(Role::class, 'administrators');
        $returnType = $method->getReturnType();

        $this->assertNotNull($returnType);
        $this->assertSame(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class, $returnType->getName());
    }

    public function test_permissions_relationship_returns_belongs_to_many(): void
    {
        $method = new \ReflectionMethod(Role::class, 'permissions');
        $returnType = $method->getReturnType();

        $this->assertNotNull($returnType);
        $this->assertSame(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class, $returnType->getName());
    }

    public function test_menus_relationship_returns_belongs_to_many(): void
    {
        $method = new \ReflectionMethod(Role::class, 'menus');
        $returnType = $method->getReturnType();

        $this->assertNotNull($returnType);
        $this->assertSame(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class, $returnType->getName());
    }

    public function test_can_returns_true_when_permission_exists(): void
    {
        $relation = Mockery::mock(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class);
        $relation->shouldReceive('where')
            ->with('slug', 'posts.edit')
            ->once()
            ->andReturnSelf();
        $relation->shouldReceive('exists')
            ->once()
            ->andReturnTrue();

        $role = Mockery::mock(Role::class)->makePartial();
        $role->shouldReceive('permissions')
            ->once()
            ->andReturn($relation);

        $this->assertTrue($role->can('posts.edit'));
    }

    public function test_cannot_returns_true_when_permission_does_not_exist(): void
    {
        $relation = Mockery::mock(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class);
        $relation->shouldReceive('where')
            ->with('slug', 'posts.delete')
            ->once()
            ->andReturnSelf();
        $relation->shouldReceive('exists')
            ->once()
            ->andReturnFalse();

        $role = Mockery::mock(Role::class)->makePartial();
        $role->shouldReceive('permissions')
            ->once()
            ->andReturn($relation);

        $this->assertTrue($role->cannot('posts.delete'));
    }

    public function test_get_permission_id_returns_empty_collection_for_empty_input(): void
    {
        $result = Role::getPermissionId([]);

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $result);
        $this->assertTrue($result->isEmpty());
    }

    public function test_administrator_role(): void
    {
        $adminRole = new Role([
            'name' => 'Administrator',
            'slug' => Role::ADMINISTRATOR,
        ]);

        $this->assertSame(Role::ADMINISTRATOR, $adminRole->slug);
        $this->assertTrue(Role::isAdministrator($adminRole->slug));
    }

    public function test_regular_role(): void
    {
        $managerRole = new Role([
            'name' => 'Manager',
            'slug' => 'manager',
        ]);

        $this->assertSame('manager', $managerRole->slug);
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
