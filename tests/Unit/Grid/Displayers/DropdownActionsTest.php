<?php

namespace Dcat\Admin\Tests\Unit\Grid\Displayers;

use Dcat\Admin\Grid\Displayers\Actions;
use Dcat\Admin\Grid\Displayers\DropdownActions;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class DropdownActionsTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(DropdownActions::class));
    }

    public function test_extends_actions(): void
    {
        $this->assertTrue(is_subclass_of(DropdownActions::class, Actions::class));
    }

    public function test_view_default_value(): void
    {
        $ref = new \ReflectionProperty(DropdownActions::class, 'view');
        $ref->setAccessible(true);

        $this->assertSame('admin::grid.dropdown-actions', $ref->getDefaultValue());
    }

    public function test_default_property_is_empty_array(): void
    {
        $ref = new \ReflectionProperty(DropdownActions::class, 'default');
        $ref->setAccessible(true);

        $this->assertSame([], $ref->getDefaultValue());
    }

    public function test_has_method_prepend(): void
    {
        $this->assertTrue(method_exists(DropdownActions::class, 'prepend'));
    }

    public function test_has_method_prepare_action(): void
    {
        $this->assertTrue(method_exists(DropdownActions::class, 'prepareAction'));
    }

    public function test_has_method_wrap_custom_action(): void
    {
        $this->assertTrue(method_exists(DropdownActions::class, 'wrapCustomAction'));
    }

    public function test_has_method_prepend_default_actions(): void
    {
        $this->assertTrue(method_exists(DropdownActions::class, 'prependDefaultActions'));
    }

    public function test_has_method_display(): void
    {
        $this->assertTrue(method_exists(DropdownActions::class, 'display'));
    }

    public function test_get_view_label_returns_empty_string(): void
    {
        $method = new \ReflectionMethod(DropdownActions::class, 'getViewLabel');
        $method->setAccessible(true);

        $ref = new \ReflectionClass(DropdownActions::class);
        $instance = $ref->newInstanceWithoutConstructor();

        $this->assertSame('', $method->invoke($instance));
    }

    public function test_get_edit_label_returns_empty_string(): void
    {
        $method = new \ReflectionMethod(DropdownActions::class, 'getEditLabel');
        $method->setAccessible(true);

        $ref = new \ReflectionClass(DropdownActions::class);
        $instance = $ref->newInstanceWithoutConstructor();

        $this->assertSame('', $method->invoke($instance));
    }

    public function test_get_quick_edit_label_returns_empty_string(): void
    {
        $method = new \ReflectionMethod(DropdownActions::class, 'getQuickEditLabel');
        $method->setAccessible(true);

        $ref = new \ReflectionClass(DropdownActions::class);
        $instance = $ref->newInstanceWithoutConstructor();

        $this->assertSame('', $method->invoke($instance));
    }

    public function test_get_delete_label_returns_empty_string(): void
    {
        $method = new \ReflectionMethod(DropdownActions::class, 'getDeleteLabel');
        $method->setAccessible(true);

        $ref = new \ReflectionClass(DropdownActions::class);
        $instance = $ref->newInstanceWithoutConstructor();

        $this->assertSame('', $method->invoke($instance));
    }

    public function test_prepend_default_actions_is_protected(): void
    {
        $method = new \ReflectionMethod(DropdownActions::class, 'prependDefaultActions');

        $this->assertTrue($method->isProtected());
    }

    public function test_wrap_custom_action_is_protected(): void
    {
        $method = new \ReflectionMethod(DropdownActions::class, 'wrapCustomAction');

        $this->assertTrue($method->isProtected());
    }
}
