<?php

namespace Dcat\Admin\Tests\Unit\Grid\Filter\Presenter;

use Dcat\Admin\Grid\Filter\Presenter\Presenter;
use Dcat\Admin\Grid\Filter\Presenter\SelectTable;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class SelectTableTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_extends_presenter(): void
    {
        $this->assertTrue(is_subclass_of(SelectTable::class, Presenter::class));
    }

    public function test_has_js_property(): void
    {
        $ref = new \ReflectionProperty(SelectTable::class, 'js');
        $this->assertTrue($ref->isPublic() || $ref->isProtected() || $ref->isPublic());
        $this->assertContains('@select-table', $ref->getDefaultValue());
    }

    public function test_has_dialog_property(): void
    {
        $ref = new \ReflectionProperty(SelectTable::class, 'dialog');
        $ref->setAccessible(true);
        $this->assertTrue($ref->isProtected());
    }

    public function test_has_style_property(): void
    {
        $ref = new \ReflectionProperty(SelectTable::class, 'style');
        $ref->setAccessible(true);
        $this->assertSame('primary', $ref->getDefaultValue());
    }

    public function test_options_method_exists(): void
    {
        $this->assertTrue(method_exists(SelectTable::class, 'options'));
    }

    public function test_model_method_exists(): void
    {
        $this->assertTrue(method_exists(SelectTable::class, 'model'));
    }

    public function test_pluck_method_exists(): void
    {
        $this->assertTrue(method_exists(SelectTable::class, 'pluck'));
    }

    public function test_dialog_width_method_exists(): void
    {
        $this->assertTrue(method_exists(SelectTable::class, 'dialogWidth'));
    }

    public function test_title_method_exists(): void
    {
        $this->assertTrue(method_exists(SelectTable::class, 'title'));
    }

    public function test_placeholder_method_exists(): void
    {
        $this->assertTrue(method_exists(SelectTable::class, 'placeholder'));
    }

    public function test_render_button_is_protected(): void
    {
        $ref = new \ReflectionMethod(SelectTable::class, 'renderButton');
        $this->assertTrue($ref->isProtected());
    }

    public function test_render_footer_is_protected(): void
    {
        $ref = new \ReflectionMethod(SelectTable::class, 'renderFooter');
        $this->assertTrue($ref->isProtected());
    }
}
