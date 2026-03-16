<?php

declare(strict_types=1);

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
        $controller = new class extends DataRuleController
        {
            public function exposeTitle(): string
            {
                return $this->title();
            }
        };

        $result = $controller->exposeTitle();
        $this->assertSame(trans('admin.data_rule.title'), $result);
        $this->assertIsString($result);
    }

    public function test_grid_returns_grid_instance(): void
    {
        $controller = new class extends DataRuleController
        {
            public function exposeGrid(): \Dcat\Admin\Grid
            {
                return $this->grid();
            }
        };

        $this->assertInstanceOf(\Dcat\Admin\Grid::class, $controller->exposeGrid());
    }

    public function test_form_returns_form_instance(): void
    {
        $controller = new DataRuleController;
        $this->assertInstanceOf(\Dcat\Admin\Form::class, $controller->form());
    }

    public function test_get_variables_html_returns_html_table(): void
    {
        $controller = new class extends DataRuleController
        {
            public function exposeVariablesHtml(): string
            {
                return $this->getVariablesHtml();
            }
        };

        $result = $controller->exposeVariablesHtml();

        $this->assertIsString($result);
        $this->assertStringContainsString('<table', $result);
        $this->assertStringContainsString('</table>', $result);
        $this->assertStringContainsString('<thead>', $result);
        $this->assertStringContainsString('<tbody>', $result);
        $this->assertStringContainsString('table-responsive', $result);
    }

    public function test_get_variables_html_contains_code_tags(): void
    {
        $controller = new class extends DataRuleController
        {
            public function exposeVariablesHtml(): string
            {
                return $this->getVariablesHtml();
            }
        };

        $result = $controller->exposeVariablesHtml();

        $this->assertStringContainsString('<code>', $result);
        $this->assertStringContainsString('</code>', $result);
    }
}
