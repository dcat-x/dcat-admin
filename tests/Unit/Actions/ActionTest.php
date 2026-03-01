<?php

namespace Dcat\Admin\Tests\Unit\Actions;

use Dcat\Admin\Actions\Action;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class ActionTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function makeAction(?string $title = null): Action
    {
        return new ConcreteTestAction($title);
    }

    public function test_constructor_sets_title(): void
    {
        $action = $this->makeAction('Test Title');
        $this->assertSame('Test Title', $action->title());
    }

    public function test_constructor_without_title(): void
    {
        $action = $this->makeAction();
        $this->assertNull($action->title());
    }

    public function test_disable_sets_disabled(): void
    {
        $action = $this->makeAction();
        $result = $action->disable();
        $this->assertSame($action, $result);
        $this->assertFalse($action->allowed());
    }

    public function test_disable_false_enables(): void
    {
        $action = $this->makeAction();
        $action->disable();
        $action->disable(false);
        $this->assertTrue($action->allowed());
    }

    public function test_allowed_returns_true_by_default(): void
    {
        $action = $this->makeAction();
        $this->assertTrue($action->allowed());
    }

    public function test_get_key_returns_null_by_default(): void
    {
        $action = $this->makeAction();
        $this->assertNull($action->getKey());
    }

    public function test_set_key_stores_key(): void
    {
        $action = $this->makeAction();
        $result = $action->setKey(42);
        $this->assertSame($action, $result);
        $this->assertSame(42, $action->getKey());
    }

    public function test_set_key_with_string(): void
    {
        $action = $this->makeAction();
        $action->setKey('abc');
        $this->assertSame('abc', $action->getKey());
    }

    public function test_selector_generates_string(): void
    {
        $action = $this->makeAction();
        $selector = $action->selector();
        $this->assertIsString($selector);
        $this->assertStringStartsWith('.act-', $selector);
    }

    public function test_selector_returns_same_value(): void
    {
        $action = $this->makeAction();
        $first = $action->selector();
        $second = $action->selector();
        $this->assertSame($first, $second);
    }

    public function test_make_static_factory(): void
    {
        $action = ConcreteTestAction::make('Factory Title');
        $this->assertInstanceOf(Action::class, $action);
        $this->assertSame('Factory Title', $action->title());
    }

    public function test_add_html_class(): void
    {
        $action = $this->makeAction();
        $result = $action->addHtmlClass('my-class');
        $this->assertSame($action, $result);
    }

    public function test_add_html_class_array(): void
    {
        $action = $this->makeAction();
        $action->addHtmlClass(['class-a', 'class-b']);

        $ref = new \ReflectionProperty($action, 'htmlClasses');
        $ref->setAccessible(true);
        $classes = $ref->getValue($action);

        $this->assertContains('class-a', $classes);
        $this->assertContains('class-b', $classes);
    }

    public function test_render_returns_empty_when_disabled(): void
    {
        $action = $this->makeAction('Disabled');
        $action->disable();
        $this->assertSame('', $action->render());
    }
}

class ConcreteTestAction extends Action
{
    // Concrete implementation for testing
}
