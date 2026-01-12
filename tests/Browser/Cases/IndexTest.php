<?php

namespace Tests\Browser\Cases;

use Dcat\Admin\Admin;
use Laravel\Dusk\Browser;
use Tests\TestCase;

/**
 * 首页功能测试.
 *
 * @group index
 */
class IndexTest extends TestCase
{
    public function test_index()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(admin_base_path('/'))
                ->pause(200)
                ->assertSeeText('Administrator')
                ->assertSeeText('Dashboard')
                ->assertSeeText('Description...')
                ->assertSeeText('New Users')
                ->assertSeeText('New Devices')
                ->assertSeeText('Tickets')
                ->assertSeeText(__('admin.documentation'))
                ->assertSeeText(__('admin.extensions'))
                ->assertSeeText(__('admin.demo'))
                ->assertSeeText('GITHUB');
        });
    }

    public function test_click_menu()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(admin_base_path('/'))
                ->within('.main-menu-content', function (Browser $browser) {
                    $browser
                        ->pause(500)
                        ->clickLink($this->translateMenuTitle('Admin'));
                    //                        ->whenTextAvailable($this->translateMenuTitle('Users'), 2)
                    //                        ->clickLink($this->translateMenuTitle('Users'))
                    //                        ->assertPathIs(admin_base_path('auth/users'))
                    //                        ->clickLink($this->translateMenuTitle('Roles'))
                    //                        ->assertPathIs(admin_base_path('auth/roles'))
                    //                        ->clickLink($this->translateMenuTitle('Permission'))
                    //                        ->assertPathIs(admin_base_path('auth/permissions'))
                    //                        ->clickLink($this->translateMenuTitle('Menu'))
                    //                        ->assertPathIs(admin_base_path('auth/menu'))
                    //                        ->clickLink($this->translateMenuTitle('Operation log'))
                    //                        ->assertPathIs(admin_base_path('auth/logs'))
                    //                        ->clickLink($this->translateMenuTitle('Helpers'))
                    //                        ->whenTextAvailable($this->translateMenuTitle('Extensions'), 2)
                    //                        ->clickLink($this->translateMenuTitle('Extensions'))
                    //                        ->assertPathIs(admin_base_path('helpers/extensions'))
                    //                        ->clickLink($this->translateMenuTitle('Scaffold'))
                    //                        ->assertPathIs(admin_base_path('helpers/scaffold'))
                    //                        ->clickLink($this->translateMenuTitle('Icons'))
                    //                        ->assertPathIs(admin_base_path('helpers/icons'));
                });
        });
    }

    /**
     * 翻译菜单标题.
     *
     * @return string
     */
    protected function translateMenuTitle($title)
    {
        return Admin::menu()->translate($title);
    }
}
