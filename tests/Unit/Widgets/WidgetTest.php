<?php

namespace Dcat\Admin\Tests\Unit\Widgets;

use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Widgets\Widget;

class ConcreteWidget extends Widget
{
    protected $view = null;
}

class WidgetTest extends TestCase
{
    public function test_options_merges_array(): void
    {
        $w = new ConcreteWidget;
        $result = $w->options(['a' => 1]);
        $this->assertSame($w, $result);
        $w->options(['b' => 2]);
        $this->assertEquals(['a' => 1, 'b' => 2], $w->getOptions());
    }

    public function test_option_getter(): void
    {
        $w = new ConcreteWidget;
        $w->options(['key' => 'val']);
        $this->assertEquals('val', $w->option('key'));
    }

    public function test_option_getter_returns_null(): void
    {
        $w = new ConcreteWidget;
        $this->assertNull($w->option('missing'));
    }

    public function test_option_setter(): void
    {
        $w = new ConcreteWidget;
        $result = $w->option('key', 'val');
        $this->assertSame($w, $result);
        $this->assertEquals('val', $w->option('key'));
    }

    public function test_get_element_class_default(): void
    {
        $w = new ConcreteWidget;
        $class = $w->getElementClass();
        // Should be based on class name with backslashes replaced
        $this->assertIsString($class);
        $this->assertStringNotContainsString('\\', $class);
    }

    public function test_set_element_class(): void
    {
        $w = new ConcreteWidget;
        $result = $w->setElementClass('custom-class');
        $this->assertSame($w, $result);
        $this->assertEquals('custom-class', $w->getElementClass());
    }

    public function test_get_element_selector(): void
    {
        $w = new ConcreteWidget;
        $w->setElementClass('my-widget');
        $this->assertEquals('.my-widget', $w->getElementSelector());
    }

    public function test_when_true_executes_callback(): void
    {
        $w = new ConcreteWidget;
        $executed = false;
        $w->when(true, function ($widget) use (&$executed) {
            $executed = true;
        });
        $this->assertTrue($executed);
    }

    public function test_when_false_skips_callback(): void
    {
        $w = new ConcreteWidget;
        $executed = false;
        $w->when(false, function ($widget) use (&$executed) {
            $executed = true;
        });
        $this->assertFalse($executed);
    }

    public function test_when_returns_this_if_callback_returns_null(): void
    {
        $w = new ConcreteWidget;
        $result = $w->when(true, function ($widget) {
            // return null implicitly
        });
        $this->assertSame($w, $result);
    }

    public function test_run_script_setting(): void
    {
        $w = new ConcreteWidget;
        $result = $w->runScript(false);
        $this->assertSame($w, $result);
        $ref = new \ReflectionProperty($w, 'runScript');
        $ref->setAccessible(true);
        $this->assertFalse($ref->getValue($w));
    }

    public function test_get_script_default_empty(): void
    {
        $w = new ConcreteWidget;
        $this->assertEquals('', $w->getScript());
    }

    public function test_magic_set_and_get(): void
    {
        $w = new ConcreteWidget;
        $w->customAttr = 'value';
        $this->assertEquals('value', $w->customAttr);
    }

    public function test_magic_get_returns_null_for_missing(): void
    {
        $w = new ConcreteWidget;
        $this->assertNull($w->nonexistent);
    }

    public function test_default_variables_structure(): void
    {
        $w = new ConcreteWidget;
        $vars = $w->defaultVariables();
        $this->assertArrayContainsKeys(['attributes', 'options', 'class', 'selector'], $vars);
    }

    public function test_make_factory(): void
    {
        $w = ConcreteWidget::make();
        $this->assertInstanceOf(ConcreteWidget::class, $w);
    }

    private function assertArrayContainsKeys(array $expectedKeys, array $actual): void
    {
        $keys = array_keys($actual);

        foreach ($expectedKeys as $key) {
            $this->assertContains($key, $keys);
        }
    }
}
