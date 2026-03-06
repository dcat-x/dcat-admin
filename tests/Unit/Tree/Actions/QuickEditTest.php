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

    public function test_is_instance_of_row_action(): void
    {
        $action = new QuickEdit;

        $this->assertInstanceOf(RowAction::class, $action);
    }

    public function test_dialog_form_dimensions_default_values(): void
    {
        $action = Mockery::mock(QuickEdit::class)->makePartial();

        $ref = new ReflectionProperty(QuickEdit::class, 'dialogFormDimensions');
        $ref->setAccessible(true);
        $dimensions = $ref->getValue($action);

        $this->assertEquals(['700px', '670px'], $dimensions);
    }

    public function test_html_method_signature_is_public_and_parameterless(): void
    {
        $method = new \ReflectionMethod(QuickEdit::class, 'html');

        $this->assertTrue($method->isPublic());
        $this->assertCount(0, $method->getParameters());
        $this->assertSame(QuickEdit::class, $method->getDeclaringClass()->getName());
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
