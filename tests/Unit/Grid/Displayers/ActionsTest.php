<?php

namespace Dcat\Admin\Tests\Unit\Grid\Displayers;

use Dcat\Admin\Grid\Displayers\AbstractDisplayer;
use Dcat\Admin\Grid\Displayers\Actions;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Support\Traits\Macroable;
use Mockery;

class ActionsTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(Actions::class));
    }

    public function test_extends_abstract_displayer(): void
    {
        $this->assertTrue(is_subclass_of(Actions::class, AbstractDisplayer::class));
    }

    public function test_uses_macroable_trait(): void
    {
        $traits = class_uses(Actions::class);
        $this->assertArrayHasKey(Macroable::class, $traits);
    }

    public function test_has_method_add(): void
    {
        $this->assertTrue(method_exists(Actions::class, 'add'));
    }

    public function test_has_method_append(): void
    {
        $this->assertTrue(method_exists(Actions::class, 'append'));
    }

    public function test_has_method_prepend(): void
    {
        $this->assertTrue(method_exists(Actions::class, 'prepend'));
    }

    public function test_has_method_view(): void
    {
        $this->assertTrue(method_exists(Actions::class, 'view'));
    }

    public function test_has_method_disable_view(): void
    {
        $this->assertTrue(method_exists(Actions::class, 'disableView'));
    }

    public function test_has_method_edit(): void
    {
        $this->assertTrue(method_exists(Actions::class, 'edit'));
    }

    public function test_has_method_disable_edit(): void
    {
        $this->assertTrue(method_exists(Actions::class, 'disableEdit'));
    }

    public function test_has_method_delete(): void
    {
        $this->assertTrue(method_exists(Actions::class, 'delete'));
    }

    public function test_has_method_disable_delete(): void
    {
        $this->assertTrue(method_exists(Actions::class, 'disableDelete'));
    }

    public function test_has_method_quick_edit(): void
    {
        $this->assertTrue(method_exists(Actions::class, 'quickEdit'));
    }

    public function test_has_method_disable_quick_edit(): void
    {
        $this->assertTrue(method_exists(Actions::class, 'disableQuickEdit'));
    }

    public function test_has_method_set_resource(): void
    {
        $this->assertTrue(method_exists(Actions::class, 'setResource'));
    }

    public function test_has_method_resource(): void
    {
        $this->assertTrue(method_exists(Actions::class, 'resource'));
    }

    public function test_has_method_display(): void
    {
        $this->assertTrue(method_exists(Actions::class, 'display'));
    }

    public function test_has_method_set_quick_edit_text(): void
    {
        $this->assertTrue(method_exists(Actions::class, 'setQuickEditText'));
    }

    public function test_has_method_set_edit_text(): void
    {
        $this->assertTrue(method_exists(Actions::class, 'setEditText'));
    }

    public function test_has_method_set_view_text(): void
    {
        $this->assertTrue(method_exists(Actions::class, 'setViewText'));
    }

    public function test_has_method_set_delete_text(): void
    {
        $this->assertTrue(method_exists(Actions::class, 'setDeleteText'));
    }

    public function test_has_method_set_quick_edit_icon(): void
    {
        $this->assertTrue(method_exists(Actions::class, 'setQuickEditIcon'));
    }

    public function test_has_method_set_edit_icon(): void
    {
        $this->assertTrue(method_exists(Actions::class, 'setEditIcon'));
    }

    public function test_has_method_set_view_icon(): void
    {
        $this->assertTrue(method_exists(Actions::class, 'setViewIcon'));
    }

    public function test_has_method_set_delete_icon(): void
    {
        $this->assertTrue(method_exists(Actions::class, 'setDeleteIcon'));
    }

    public function test_actions_default_value(): void
    {
        $ref = new \ReflectionProperty(Actions::class, 'actions');
        $ref->setAccessible(true);

        $expected = [
            'view' => true,
            'edit' => true,
            'quickEdit' => false,
            'delete' => true,
        ];

        $this->assertSame($expected, $ref->getDefaultValue());
    }

    public function test_appends_default_empty_array(): void
    {
        $ref = new \ReflectionProperty(Actions::class, 'appends');
        $ref->setAccessible(true);

        $this->assertSame([], $ref->getDefaultValue());
    }

    public function test_prepends_default_empty_array(): void
    {
        $ref = new \ReflectionProperty(Actions::class, 'prepends');
        $ref->setAccessible(true);

        $this->assertSame([], $ref->getDefaultValue());
    }

    public function test_custom_default_empty_array(): void
    {
        $ref = new \ReflectionProperty(Actions::class, 'custom');
        $ref->setAccessible(true);

        $this->assertSame([], $ref->getDefaultValue());
    }
}
