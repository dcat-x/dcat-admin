<?php

namespace Dcat\Admin\Tests\Unit\Http\Controllers;

use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Http\Controllers\DataRuleController;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class DataRuleControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('admin.database.menu_model', \Dcat\Admin\Models\Menu::class);
    }

    public function test_controller_extends_admin_controller(): void
    {
        $controller = new DataRuleController;

        $this->assertInstanceOf(AdminController::class, $controller);
    }

    public function test_title_returns_translated_data_rule_title(): void
    {
        $controller = new DataRuleController;

        $reflection = new \ReflectionMethod($controller, 'title');
        $reflection->setAccessible(true);

        $result = $reflection->invoke($controller);
        $this->assertEquals(trans('admin.data_rule.title'), $result);
        $this->assertIsString($result);
    }

    public function test_grid_method_exists_and_is_protected(): void
    {
        $this->assertTrue(method_exists(DataRuleController::class, 'grid'));

        $reflection = new \ReflectionMethod(DataRuleController::class, 'grid');
        $this->assertTrue($reflection->isProtected());
    }

    public function test_detail_method_exists_and_is_protected(): void
    {
        $this->assertTrue(method_exists(DataRuleController::class, 'detail'));

        $reflection = new \ReflectionMethod(DataRuleController::class, 'detail');
        $this->assertTrue($reflection->isProtected());

        $params = $reflection->getParameters();
        $this->assertCount(1, $params);
        $this->assertEquals('id', $params[0]->getName());
    }

    public function test_form_method_exists_and_is_public(): void
    {
        $this->assertTrue(method_exists(DataRuleController::class, 'form'));

        $reflection = new \ReflectionMethod(DataRuleController::class, 'form');
        $this->assertTrue($reflection->isPublic());
    }

    public function test_get_variables_html_returns_html_table(): void
    {
        $controller = new DataRuleController;

        $reflection = new \ReflectionMethod($controller, 'getVariablesHtml');
        $reflection->setAccessible(true);

        $result = $reflection->invoke($controller);

        $this->assertIsString($result);
        $this->assertStringContainsString('<table', $result);
        $this->assertStringContainsString('</table>', $result);
        $this->assertStringContainsString('<thead>', $result);
        $this->assertStringContainsString('<tbody>', $result);
        $this->assertStringContainsString('table-responsive', $result);
    }

    public function test_get_variables_html_contains_code_tags(): void
    {
        $controller = new DataRuleController;

        $reflection = new \ReflectionMethod($controller, 'getVariablesHtml');
        $reflection->setAccessible(true);

        $result = $reflection->invoke($controller);

        $this->assertStringContainsString('<code>', $result);
        $this->assertStringContainsString('</code>', $result);
    }
}
