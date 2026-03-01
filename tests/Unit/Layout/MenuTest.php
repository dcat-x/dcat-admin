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

    public function test_has_register_method(): void
    {
        $this->assertTrue(method_exists(Menu::class, 'register'));
    }

    public function test_has_add_method(): void
    {
        $this->assertTrue(method_exists(Menu::class, 'add'));
    }

    public function test_has_to_html_method(): void
    {
        $this->assertTrue(method_exists(Menu::class, 'toHtml'));
    }

    public function test_has_render_method(): void
    {
        $this->assertTrue(method_exists(Menu::class, 'render'));
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
