<?php

namespace Dcat\Admin\Tests\Unit\Grid\Displayers;

use Dcat\Admin\Grid\Displayers\Checkbox;
use Dcat\Admin\Grid\Displayers\Editable;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class CheckboxTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(Checkbox::class));
    }

    public function test_extends_editable(): void
    {
        $this->assertTrue(is_subclass_of(Checkbox::class, Editable::class));
    }

    public function test_type_is_checkbox(): void
    {
        $ref = new \ReflectionProperty(Checkbox::class, 'type');
        $ref->setAccessible(true);

        $this->assertSame('checkbox', $ref->getDefaultValue());
    }

    public function test_view_is_checkbox_view(): void
    {
        $ref = new \ReflectionProperty(Checkbox::class, 'view');
        $ref->setAccessible(true);

        $this->assertSame('admin::grid.displayer.editinline.checkbox', $ref->getDefaultValue());
    }

    public function test_has_method_display(): void
    {
        $this->assertTrue(method_exists(Checkbox::class, 'display'));
    }

    public function test_has_method_render_checkbox(): void
    {
        $this->assertTrue(method_exists(Checkbox::class, 'renderCheckbox'));
    }

    public function test_has_method_get_value(): void
    {
        $this->assertTrue(method_exists(Checkbox::class, 'getValue'));
    }

    public function test_has_method_get_original(): void
    {
        $this->assertTrue(method_exists(Checkbox::class, 'getOriginal'));
    }

    public function test_render_checkbox_is_protected(): void
    {
        $method = new \ReflectionMethod(Checkbox::class, 'renderCheckbox');

        $this->assertTrue($method->isProtected());
    }

    public function test_get_value_is_protected(): void
    {
        $method = new \ReflectionMethod(Checkbox::class, 'getValue');

        $this->assertTrue($method->isProtected());
    }

    public function test_get_original_is_protected(): void
    {
        $method = new \ReflectionMethod(Checkbox::class, 'getOriginal');

        $this->assertTrue($method->isProtected());
    }
}
