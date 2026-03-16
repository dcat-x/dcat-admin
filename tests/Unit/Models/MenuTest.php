<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Models;

use Dcat\Admin\Models\Menu;
use Dcat\Admin\Models\Permission;
use Dcat\Admin\Models\Role;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\EloquentSortable\Sortable;

class MenuTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('admin.database.menu_table', 'admin_menu');
        $this->app['config']->set('admin.database.menu_model', Menu::class);
        $this->app['config']->set('admin.database.roles_model', Role::class);
        $this->app['config']->set('admin.database.permissions_model', Permission::class);
        $this->app['config']->set('admin.database.role_menu_table', 'admin_role_menu');
        $this->app['config']->set('admin.database.permission_menu_table', 'admin_permission_menu');
    }

    public function test_fillable_attributes(): void
    {
        $menu = new Menu;

        $fillable = $menu->getFillable();

        $this->assertContains('parent_id', $fillable);
        $this->assertContains('order', $fillable);
        $this->assertContains('title', $fillable);
        $this->assertContains('icon', $fillable);
        $this->assertContains('uri', $fillable);
        $this->assertContains('extension', $fillable);
        $this->assertContains('show', $fillable);
        $this->assertCount(7, $fillable);
    }

    public function test_table_name_from_config(): void
    {
        $this->app['config']->set('admin.database.menu_table', 'custom_menu');

        $menu = new Menu;

        $this->assertSame('custom_menu', $menu->getTable());
    }

    public function test_connection_from_config(): void
    {
        $this->app['config']->set('admin.database.connection', 'mysql');

        $menu = new Menu;

        $this->assertSame('mysql', $menu->getConnectionName());
    }

    public function test_connection_defaults_to_database_default(): void
    {
        $this->app['config']->set('admin.database.connection', '');
        $this->app['config']->set('database.default', 'testing');

        $menu = new Menu;

        $this->assertSame('testing', $menu->getConnectionName());
    }

    public function test_with_permission_returns_true_when_both_enabled(): void
    {
        $this->app['config']->set('admin.menu.bind_permission', true);
        $this->app['config']->set('admin.permission.enable', true);

        $this->assertTrue(Menu::withPermission());
    }

    public function test_with_permission_returns_false_when_menu_disabled(): void
    {
        $this->app['config']->set('admin.menu.bind_permission', false);
        $this->app['config']->set('admin.permission.enable', true);

        $this->assertFalse(Menu::withPermission());
    }

    public function test_with_permission_returns_false_when_permission_disabled(): void
    {
        $this->app['config']->set('admin.menu.bind_permission', true);
        $this->app['config']->set('admin.permission.enable', false);

        $this->assertFalse(Menu::withPermission());
    }

    public function test_with_role_returns_true_when_permission_enabled(): void
    {
        $this->app['config']->set('admin.permission.enable', true);

        $this->assertTrue(Menu::withRole());
    }

    public function test_with_role_returns_false_when_permission_disabled(): void
    {
        $this->app['config']->set('admin.permission.enable', false);

        $this->assertFalse(Menu::withRole());
    }

    public function test_sortable_config(): void
    {
        $menu = new Menu;

        $this->assertInstanceOf(Sortable::class, $menu);

        $reflection = new \ReflectionProperty($menu, 'sortable');
        $sortable = $reflection->getValue($menu);

        $this->assertTrue($sortable['sort_when_creating']);
    }

    public function test_roles_relationship_exists(): void
    {
        $menu = new Menu;

        $relation = $menu->roles();

        $this->assertInstanceOf(BelongsToMany::class, $relation);
    }

    public function test_permissions_relationship_exists(): void
    {
        $menu = new Menu;

        $relation = $menu->permissions();

        $this->assertInstanceOf(BelongsToMany::class, $relation);
    }
}
