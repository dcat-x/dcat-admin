<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Traits;

use Dcat\Admin\Models\Administrator;
use Dcat\Admin\Models\DataRule;
use Dcat\Admin\Models\Permission;
use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Traits\HasPermissions;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FakePermissionUserForHasPermissionsTest
{
    use HasPermissions;

    public function __construct(private array $permissionItems = []) {}

    public function getKeyName(): string
    {
        return 'id';
    }

    public function isAdministrator(): bool
    {
        return false;
    }

    public function allPermissions(): Collection
    {
        return collect($this->permissionItems)->keyBy('id');
    }
}

class FakeInheritedRoleUserForHasPermissionsTest
{
    use HasPermissions;

    public $roles;

    public int $allRolesCalls = 0;

    public function __construct()
    {
        $this->roles = collect();
    }

    public function allRoles(): Collection
    {
        $this->allRolesCalls++;

        return collect([
            (object) ['id' => 8, 'slug' => 'editor'],
            (object) ['id' => 9, 'slug' => 'auditor'],
        ]);
    }
}

class FakePermissionKeyCacheUserForHasPermissionsTest
{
    use HasPermissions;

    public int $allPermissionsCalls = 0;

    public function getKeyName(): string
    {
        return 'id';
    }

    public function isAdministrator(): bool
    {
        return false;
    }

    public function allPermissions(): Collection
    {
        $this->allPermissionsCalls++;

        return collect([
            (object) ['id' => 1, 'slug' => 'users.index', 'permission_key' => 'user:view'],
            (object) ['id' => 2, 'slug' => 'users.edit', 'permission_key' => 'user:edit'],
        ])->keyBy('id');
    }
}

class HasPermissionsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('admin.database.users_model', Administrator::class);
        $this->app['config']->set('admin.database.permissions_model', Permission::class);
        $this->app['config']->set('admin.data_permission.enable', true);
    }

    public function test_administrator_fillable_attributes(): void
    {
        $admin = new Administrator;

        // 验证基本的可填充属性
        $fillable = $admin->getFillable();

        $this->assertContains('username', $fillable);
        $this->assertContains('password', $fillable);
        $this->assertContains('name', $fillable);
    }

    public function test_can_matches_permission_slug_and_id(): void
    {
        $user = new FakePermissionUserForHasPermissionsTest([
            (object) ['id' => 1, 'slug' => 'post.create'],
            (object) ['id' => 2, 'slug' => 'post.delete'],
        ]);

        $this->assertTrue($user->can('post.create'));
        $this->assertTrue($user->can(2));
        $this->assertFalse($user->can('not-exists'));
        $this->assertFalse($user->can(999));
    }

    public function test_cannot_is_inverse_of_can(): void
    {
        $user = new FakePermissionUserForHasPermissionsTest([
            (object) ['id' => 1, 'slug' => 'post.create'],
        ]);

        $this->assertFalse($user->cannot('post.create'));
        $this->assertTrue($user->cannot('post.update'));
    }

    public function test_is_role_uses_all_roles_when_available(): void
    {
        $user = new FakeInheritedRoleUserForHasPermissionsTest;

        $this->assertTrue($user->isRole('editor'));
        $this->assertTrue($user->isRole('9'));
        $this->assertFalse($user->isRole('manager'));
    }

    public function test_in_roles_uses_all_roles_when_available(): void
    {
        $user = new FakeInheritedRoleUserForHasPermissionsTest;

        $this->assertTrue($user->inRoles(['editor']));
        $this->assertTrue($user->inRoles(['8']));
        $this->assertTrue($user->inRoles(['auditor', 'manager']));
        $this->assertFalse($user->inRoles(['manager']));
    }

    public function test_can_permission_key_uses_cached_key_map(): void
    {
        $user = new FakePermissionKeyCacheUserForHasPermissionsTest;

        $this->assertTrue($user->canPermissionKey('user:view'));
        $this->assertFalse($user->canPermissionKey('user:delete'));
        $this->assertSame(1, $user->allPermissionsCalls);
    }

    public function test_role_checks_reuse_cached_roles(): void
    {
        $user = new FakeInheritedRoleUserForHasPermissionsTest;

        $this->assertTrue($user->isRole('editor'));
        $this->assertTrue($user->inRoles(['auditor']));
        $this->assertTrue($user->isRole('9'));
        $this->assertSame(1, $user->allRolesCalls);
    }

    public function test_get_data_rules_qualifies_join_columns_and_hydrates_data_rule(): void
    {
        $this->app['config']->set('admin.database.data_rules_table', 'test_data_rules');
        $this->app['config']->set('admin.database.data_rules_model', DataRule::class);
        $this->app['config']->set('admin.database.role_data_rules_table', 'test_role_data_rules');

        Schema::create('test_data_rules', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('menu_id');
            $table->string('name');
            $table->boolean('status');
            $table->integer('order');
        });
        Schema::create('test_role_data_rules', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('data_rule_id');
        });

        DB::table('test_data_rules')->insert([
            'id' => 10,
            'menu_id' => 42,
            'name' => 'Owned rule',
            'status' => 1,
            'order' => 1,
        ]);
        DB::table('test_role_data_rules')->insert([
            'id' => 100,
            'role_id' => 8,
            'data_rule_id' => 10,
        ]);

        try {
            $rules = (new FakeInheritedRoleUserForHasPermissionsTest)->getDataRules(42);
        } finally {
            Schema::dropIfExists('test_role_data_rules');
            Schema::dropIfExists('test_data_rules');
        }

        $this->assertCount(1, $rules);
        $this->assertInstanceOf(DataRule::class, $rules->first());
        $this->assertSame(10, $rules->first()->getKey());
        $this->assertSame('Owned rule', $rules->first()->name);
    }
}
