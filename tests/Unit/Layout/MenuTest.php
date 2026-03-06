<?php

namespace Dcat\Admin\Tests\Unit\Layout;

use Dcat\Admin\Layout\Menu;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use Mockery;

class MenuTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function makeMenu(): Menu
    {
        return new Menu;
    }

    protected function mockAdminGuardUserCanSeeMenu(bool $canSee = true): void
    {
        $this->app['config']->set('admin.auth.guard', 'admin');

        $user = Mockery::mock();
        $user->shouldReceive('canSeeMenu')->andReturn($canSee);

        $guard = Mockery::mock(\Illuminate\Contracts\Auth\Guard::class);
        $guard->shouldReceive('user')->andReturn($user);
        $guard->shouldReceive('check')->andReturn(true);
        $guard->shouldReceive('id')->andReturn(1);

        Auth::shouldReceive('guard')->with('admin')->andReturn($guard);
    }

    public function test_view_sets_custom_view(): void
    {
        $menu = $this->makeMenu();
        $result = $menu->view('custom.menu.view');
        $this->assertSame($menu, $result);

        $ref = new \ReflectionProperty($menu, 'view');
        $ref->setAccessible(true);
        $this->assertSame('custom.menu.view', $ref->getValue($menu));
    }

    public function test_default_view(): void
    {
        $menu = $this->makeMenu();
        $ref = new \ReflectionProperty($menu, 'view');
        $ref->setAccessible(true);
        $this->assertSame('admin::partials.menu', $ref->getValue($menu));
    }

    public function test_translate_returns_text_when_no_translation(): void
    {
        $menu = $this->makeMenu();
        $result = $menu->translate('Some Unknown Menu');
        $this->assertSame('Some Unknown Menu', $result);
    }

    public function test_get_path_with_empty_uri(): void
    {
        $menu = $this->makeMenu();
        $this->assertSame('', $menu->getPath(''));
    }

    public function test_get_url_with_empty_uri(): void
    {
        $menu = $this->makeMenu();
        $this->assertSame('', $menu->getUrl(''));
    }

    public function test_is_active_empty_children_no_uri(): void
    {
        $menu = $this->makeMenu();
        $item = ['children' => [], 'uri' => ''];
        $this->assertFalse($menu->isActive($item));
    }

    public function test_visible_with_show_false(): void
    {
        $this->app['config']->set('admin.auth.guard', 'admin');

        $user = Mockery::mock();
        $user->shouldReceive('isAdministrator')->andReturn(true);
        $user->shouldReceive('canSeeMenu')->andReturn(true);

        $guard = Mockery::mock(\Illuminate\Contracts\Auth\Guard::class);
        $guard->shouldReceive('user')->andReturn($user);
        $guard->shouldReceive('check')->andReturn(true);
        $guard->shouldReceive('id')->andReturn(1);

        Auth::shouldReceive('guard')->with('admin')->andReturn($guard);

        $menu = $this->makeMenu();
        $item = ['show' => false, 'extension' => null, 'permission_id' => null, 'roles' => [], 'permissions' => []];
        $this->assertFalse($menu->visible($item));
    }

    public function test_helper_nodes_static_property(): void
    {
        $ref = new \ReflectionProperty(Menu::class, 'helperNodes');
        $ref->setAccessible(true);
        $nodes = $ref->getValue();
        $this->assertIsArray($nodes);
        $this->assertNotEmpty($nodes);
    }

    public function test_register_method_signature_is_public_and_parameterless(): void
    {
        $method = new \ReflectionMethod(Menu::class, 'register');

        $this->assertTrue($method->isPublic());
        $this->assertCount(0, $method->getParameters());
    }

    public function test_add_method_signature_accepts_nodes_and_priority(): void
    {
        $method = new \ReflectionMethod(Menu::class, 'add');
        $params = $method->getParameters();

        $this->assertTrue($method->isPublic());
        $this->assertCount(2, $params);
        $this->assertSame('nodes', $params[0]->getName());
        $this->assertSame('priority', $params[1]->getName());
    }

    public function test_render_outputs_menu_item_html_when_visible(): void
    {
        config()->set('admin.menu.role_bind_menu', false);
        $this->mockAdminGuardUserCanSeeMenu();

        $menu = $this->makeMenu();
        $item = [
            'id' => 1,
            'title' => 'Dashboard',
            'icon' => 'fa-home',
            'uri' => 'dashboard',
            'children' => [],
        ];

        $html = $menu->render($item);

        $this->assertStringContainsString('Dashboard', $html);
        $this->assertStringContainsString('nav-item', $html);
    }

    public function test_to_html_renders_multiple_visible_nodes(): void
    {
        config()->set('admin.menu.role_bind_menu', false);
        $this->mockAdminGuardUserCanSeeMenu();

        $menu = $this->makeMenu();
        $nodes = [
            ['id' => 1, 'title' => 'Dashboard', 'icon' => 'fa-home', 'uri' => 'dashboard', 'parent_id' => 0],
            ['id' => 2, 'title' => 'Users', 'icon' => 'fa-user', 'uri' => 'users', 'parent_id' => 0],
        ];

        $html = $menu->toHtml($nodes);

        $this->assertStringContainsString('Dashboard', $html);
        $this->assertStringContainsString('Users', $html);
    }

    public function test_check_permission_is_protected(): void
    {
        $ref = new \ReflectionMethod(Menu::class, 'checkPermission');
        $this->assertTrue($ref->isProtected());
    }

    public function test_check_extension_is_protected(): void
    {
        $ref = new \ReflectionMethod(Menu::class, 'checkExtension');
        $this->assertTrue($ref->isProtected());
    }
}
