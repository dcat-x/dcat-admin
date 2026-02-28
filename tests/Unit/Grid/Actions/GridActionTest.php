<?php

namespace Dcat\Admin\Tests\Unit\Grid\Actions;

use Dcat\Admin\Actions\Action;
use Dcat\Admin\Tests\TestCase;

// Concrete subclass for testing
class ConcreteAction extends Action
{
    public function render(): string
    {
        return '';
    }
}

class GridActionTest extends TestCase
{
    public function test_constructor_with_title(): void
    {
        $action = new ConcreteAction('Test Action');
        $this->assertEquals('Test Action', $action->title());
    }

    public function test_constructor_without_title(): void
    {
        $action = new ConcreteAction;
        $this->assertNull($action->title());
    }

    public function test_disable_and_allowed(): void
    {
        $action = new ConcreteAction;
        $this->assertTrue($action->allowed());

        $action->disable();
        $this->assertFalse($action->allowed());
    }

    public function test_disable_returns_this(): void
    {
        $action = new ConcreteAction;
        $result = $action->disable();
        $this->assertSame($action, $result);
    }

    public function test_disable_with_false_re_enables(): void
    {
        $action = new ConcreteAction;
        $action->disable();
        $action->disable(false);
        $this->assertTrue($action->allowed());
    }

    public function test_set_key_and_get_key(): void
    {
        $action = new ConcreteAction;
        $result = $action->setKey(42);
        $this->assertSame($action, $result);
        $this->assertEquals(42, $action->getKey());
    }

    public function test_get_key_returns_null_by_default(): void
    {
        $action = new ConcreteAction;
        $this->assertNull($action->getKey());
    }

    public function test_set_key_with_string(): void
    {
        $action = new ConcreteAction;
        $action->setKey('abc-123');
        $this->assertEquals('abc-123', $action->getKey());
    }

    public function test_selector_generates_string(): void
    {
        $action = new ConcreteAction;
        $selector = $action->selector();
        $this->assertStringStartsWith('.act-', $selector);
    }

    public function test_selector_is_cached(): void
    {
        $action = new ConcreteAction;
        $first = $action->selector();
        $second = $action->selector();
        $this->assertSame($first, $second);
    }

    public function test_add_html_class_with_string(): void
    {
        $action = new ConcreteAction;
        $result = $action->addHtmlClass('btn-primary');
        $this->assertSame($action, $result);
    }

    public function test_add_html_class_with_array(): void
    {
        $action = new ConcreteAction;
        $action->addHtmlClass(['btn', 'btn-sm']);
        // verify through reflection
        $ref = new \ReflectionProperty($action, 'htmlClasses');
        $ref->setAccessible(true);
        $classes = $ref->getValue($action);
        $this->assertContains('btn', $classes);
        $this->assertContains('btn-sm', $classes);
    }

    public function test_make_factory_method(): void
    {
        $action = ConcreteAction::make('Factory Title');
        $this->assertInstanceOf(ConcreteAction::class, $action);
        $this->assertEquals('Factory Title', $action->title());
    }
}
