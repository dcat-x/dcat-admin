<?php

namespace Dcat\Admin\Tests\Unit\Grid\Displayers;

use Dcat\Admin\Grid\Displayers\ContextMenuActions;
use Dcat\Admin\Grid\Displayers\DropdownActions;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class ContextMenuActionsTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(ContextMenuActions::class));
    }

    public function test_extends_dropdown_actions(): void
    {
        $this->assertTrue(is_subclass_of(ContextMenuActions::class, DropdownActions::class));
    }

    public function test_element_id_default_value(): void
    {
        $ref = new \ReflectionProperty(ContextMenuActions::class, 'elementId');
        $ref->setAccessible(true);

        $this->assertSame('grid-context-menu', $ref->getDefaultValue());
    }

    public function test_has_method_add_script(): void
    {
        $this->assertTrue(method_exists(ContextMenuActions::class, 'addScript'));
    }

    public function test_has_method_display(): void
    {
        $this->assertTrue(method_exists(ContextMenuActions::class, 'display'));
    }

    public function test_add_script_is_protected(): void
    {
        $method = new \ReflectionMethod(ContextMenuActions::class, 'addScript');

        $this->assertTrue($method->isProtected());
    }

    public function test_display_is_public(): void
    {
        $method = new \ReflectionMethod(ContextMenuActions::class, 'display');

        $this->assertTrue($method->isPublic());
    }
}
