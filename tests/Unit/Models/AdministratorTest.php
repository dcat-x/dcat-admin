<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Models;

use Dcat\Admin\Models\Administrator;
use Dcat\Admin\Models\Department;
use Dcat\Admin\Models\Role;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdministratorTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('admin.database.users_table', 'admin_users');
        $this->app['config']->set('admin.database.users_model', Administrator::class);
        $this->app['config']->set('admin.database.roles_model', Role::class);
        $this->app['config']->set('admin.database.roles_table', 'admin_roles');
        $this->app['config']->set('admin.database.role_users_table', 'admin_role_users');
        $this->app['config']->set('admin.database.departments_model', Department::class);
        $this->app['config']->set('admin.database.department_users_table', 'admin_department_users');
    }

    public function test_default_id_constant(): void
    {
        $this->assertSame(1, Administrator::DEFAULT_ID);
    }

    public function test_fillable_attributes(): void
    {
        $admin = new Administrator;

        $fillable = $admin->getFillable();

        $this->assertContains('username', $fillable);
        $this->assertContains('password', $fillable);
        $this->assertContains('name', $fillable);
        $this->assertContains('avatar', $fillable);
        $this->assertCount(4, $fillable);
    }

    public function test_table_name_from_config(): void
    {
        $this->app['config']->set('admin.database.users_table', 'custom_users');

        $admin = new Administrator;

        $this->assertSame('custom_users', $admin->getTable());
    }

    public function test_connection_from_config(): void
    {
        $this->app['config']->set('admin.database.connection', 'mysql');

        $admin = new Administrator;

        $this->assertSame('mysql', $admin->getConnectionName());
    }

    public function test_can_see_menu_always_returns_true(): void
    {
        $admin = new Administrator;

        $this->assertTrue($admin->canSeeMenu([]));
        $this->assertTrue($admin->canSeeMenu(['title' => 'Dashboard']));
        $this->assertTrue($admin->canSeeMenu(null));
    }

    public function test_implements_authenticatable_contract(): void
    {
        $admin = new Administrator;

        $this->assertInstanceOf(AuthenticatableContract::class, $admin);
    }

    public function test_implements_authorizable(): void
    {
        $admin = new Administrator;

        $this->assertInstanceOf(Authorizable::class, $admin);
    }

    public function test_roles_relationship_exists(): void
    {
        $admin = new Administrator;

        $relation = $admin->roles();

        $this->assertInstanceOf(BelongsToMany::class, $relation);
    }

    public function test_departments_relationship_exists(): void
    {
        $admin = new Administrator;

        $relation = $admin->departments();

        $this->assertInstanceOf(BelongsToMany::class, $relation);
    }

    public function test_get_primary_department_id_returns_null_when_disabled(): void
    {
        $this->app['config']->set('admin.department.enable', false);

        $admin = new Administrator;

        $this->assertNull($admin->primary_department_id);
    }

    public function test_department_ids_query_qualifies_the_department_primary_key(): void
    {
        $this->app['config']->set('admin.department.enable', true);

        Schema::create('admin_users', function (Blueprint $table): void {
            $table->id();
            $table->string('username');
        });
        Schema::create('admin_departments', function (Blueprint $table): void {
            $table->id();
        });
        Schema::create('admin_department_users', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('department_id');
            $table->unsignedBigInteger('user_id');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });

        DB::table('admin_users')->insert(['id' => 1, 'username' => 'admin']);
        DB::table('admin_departments')->insert(['id' => 10]);
        DB::table('admin_department_users')->insert([
            'id' => 100,
            'department_id' => 10,
            'user_id' => 1,
        ]);

        $admin = Administrator::query()->findOrFail(1);

        $this->assertSame('10', $admin->department_ids);
    }

    public function test_all_roles_is_cached_for_repeated_calls(): void
    {
        $admin = new class extends Administrator
        {
            public int $departmentRoleCalls = 0;

            public function getDepartmentRoles()
            {
                $this->departmentRoleCalls++;

                return collect([
                    (object) ['id' => 2, 'slug' => 'editor'],
                ]);
            }
        };

        $admin->setRelation('roles', collect([
            (object) ['id' => 1, 'slug' => 'administrator'],
        ]));

        $first = $admin->allRoles();
        $second = $admin->allRoles();

        $this->assertInstanceOf(Collection::class, $first);
        $this->assertCount(2, $first);
        $this->assertSame($first->pluck('id')->all(), $second->pluck('id')->all());
        $this->assertSame(1, $admin->departmentRoleCalls);
    }
}
