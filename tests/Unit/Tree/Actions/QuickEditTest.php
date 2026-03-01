<?php

namespace Dcat\Admin\Tests\Unit\Tree\Actions;

use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Tree\Actions\QuickEdit;
use Dcat\Admin\Tree\RowAction;
use Mockery;
use ReflectionProperty;

class QuickEditTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(QuickEdit::class));
    }

    public function test_extends_row_action(): void
    {
        $action = Mockery::mock(QuickEdit::class)->makePartial();

        $this->assertInstanceOf(RowAction::class, $action);
    }

    public function test_has_html_method(): void
    {
        $this->assertTrue(method_exists(QuickEdit::class, 'html'));
    }

    public function test_has_dialog_form_dimensions(): void
    {
        $action = Mockery::mock(QuickEdit::class)->makePartial();

        $ref = new ReflectionProperty(QuickEdit::class, 'dialogFormDimensions');
        $ref->setAccessible(true);

        $this->assertIsArray($ref->getValue($action));
    }

    public function test_dialog_form_dimensions_default_values(): void
    {
        $action = Mockery::mock(QuickEdit::class)->makePartial();

        $ref = new ReflectionProperty(QuickEdit::class, 'dialogFormDimensions');
        $ref->setAccessible(true);
        $dimensions = $ref->getValue($action);

        $this->assertEquals(['700px', '670px'], $dimensions);
    }

    public function test_html_contains_quick_edit_class(): void
    {
        $action = Mockery::mock(QuickEdit::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $action->shouldReceive('getKey')->andReturn(3);
        $action->shouldReceive('resource')->andReturn('/admin/categories');

        $html = $action->html();

        $this->assertStringContainsString('tree-quick-edit', $html);
    }
}
