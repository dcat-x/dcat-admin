<?php

namespace Dcat\Admin\Tests\Unit\Show;

use Dcat\Admin\Show;
use Dcat\Admin\Show\Panel;
use Dcat\Admin\Show\Tools;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Support\Collection;
use Mockery;

class PanelTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function makeShow(): Show
    {
        return new Show(['name' => 'Test', 'email' => 'test@example.com']);
    }

    public function test_constructor_initializes_variables(): void
    {
        $show = $this->makeShow();
        $panel = new Panel($show);

        $variables = $panel->variables();

        $this->assertArrayHasKey('fields', $variables);
        $this->assertArrayHasKey('tools', $variables);
        $this->assertArrayHasKey('rows', $variables);
        $this->assertArrayHasKey('style', $variables);
        $this->assertArrayHasKey('title', $variables);
    }

    public function test_fields_initialized_as_collection(): void
    {
        $show = $this->makeShow();
        $panel = new Panel($show);

        $variables = $panel->variables();
        $this->assertInstanceOf(Collection::class, $variables['fields']);
    }

    public function test_tools_initialized_as_tools_instance(): void
    {
        $show = $this->makeShow();
        $panel = new Panel($show);

        $variables = $panel->variables();
        $this->assertInstanceOf(Tools::class, $variables['tools']);
    }

    public function test_default_style_is_default(): void
    {
        $show = $this->makeShow();
        $panel = new Panel($show);

        $variables = $panel->variables();
        $this->assertSame('default', $variables['style']);
    }

    public function test_title_sets_panel_title(): void
    {
        $show = $this->makeShow();
        $panel = new Panel($show);

        $result = $panel->title('Custom Title');

        $this->assertSame($panel, $result);

        $variables = $panel->variables();
        $this->assertSame('Custom Title', $variables['title']);
    }

    public function test_style_sets_panel_style(): void
    {
        $show = $this->makeShow();
        $panel = new Panel($show);

        $result = $panel->style('info');

        $this->assertSame($panel, $result);

        $variables = $panel->variables();
        $this->assertSame('info', $variables['style']);
    }

    public function test_fill_sets_fields(): void
    {
        $show = $this->makeShow();
        $panel = new Panel($show);

        $fields = new Collection(['field1', 'field2']);
        $result = $panel->fill($fields);

        $this->assertSame($panel, $result);

        $variables = $panel->variables();
        $this->assertSame($fields, $variables['fields']);
    }

    public function test_tools_returns_tools_when_no_argument(): void
    {
        $show = $this->makeShow();
        $panel = new Panel($show);

        $tools = $panel->tools();

        $this->assertInstanceOf(Tools::class, $tools);
    }

    public function test_tools_calls_callback_with_tools(): void
    {
        $show = $this->makeShow();
        $panel = new Panel($show);
        $callbackReceived = null;

        $panel->tools(function ($tools) use (&$callbackReceived) {
            $callbackReceived = $tools;
        });

        $this->assertInstanceOf(Tools::class, $callbackReceived);
    }

    public function test_set_parent_changes_parent(): void
    {
        $show1 = $this->makeShow();
        $show2 = $this->makeShow();

        $panel = new Panel($show1);
        $result = $panel->setParent($show2);

        $this->assertSame($panel, $result);
        $this->assertSame($show2, $panel->parent());
    }

    public function test_parent_returns_show_instance(): void
    {
        $show = $this->makeShow();
        $panel = new Panel($show);

        $this->assertSame($show, $panel->parent());
    }

    public function test_view_sets_custom_view(): void
    {
        $show = $this->makeShow();
        $panel = new Panel($show);

        $result = $panel->view('custom::show.panel');

        $this->assertSame($panel, $result);

        // Verify via reflection
        $ref = new \ReflectionProperty(Panel::class, 'view');
        $ref->setAccessible(true);
        $this->assertSame('custom::show.panel', $ref->getValue($panel));
    }

    public function test_with_merges_variables(): void
    {
        $show = $this->makeShow();
        $panel = new Panel($show);

        $result = $panel->with(['custom_key' => 'custom_value']);

        $this->assertSame($panel, $result);

        $variables = $panel->variables();
        $this->assertArrayHasKey('custom_key', $variables);
        $this->assertSame('custom_value', $variables['custom_key']);
    }

    public function test_wrap_sets_wrapper_closure(): void
    {
        $show = $this->makeShow();
        $panel = new Panel($show);

        $this->assertFalse($panel->hasWrapper());

        $wrapper = function ($view) {
            return "<custom>{$view->render()}</custom>";
        };

        $result = $panel->wrap($wrapper);

        $this->assertSame($panel, $result);
        $this->assertTrue($panel->hasWrapper());
    }

    public function test_has_wrapper_returns_false_by_default(): void
    {
        $show = $this->makeShow();
        $panel = new Panel($show);

        $this->assertFalse($panel->hasWrapper());
    }

    public function test_rows_from_parent_included_in_variables(): void
    {
        $show = $this->makeShow();
        $panel = new Panel($show);

        $variables = $panel->variables();
        $this->assertInstanceOf(Collection::class, $variables['rows']);
    }
}
