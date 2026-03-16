<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Tree\Actions;

use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Tree\Actions\Edit;
use Dcat\Admin\Tree\RowAction;
use Mockery;

class EditTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_extends_row_action(): void
    {
        $action = Mockery::mock(Edit::class)->makePartial();

        $this->assertInstanceOf(RowAction::class, $action);
    }

    public function test_html_signature_has_no_parameters(): void
    {
        $method = new \ReflectionMethod(Edit::class, 'html');

        $this->assertSame(0, $method->getNumberOfParameters());
    }

    public function test_html_contains_edit_url(): void
    {
        $action = Mockery::mock(Edit::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $action->shouldReceive('getKey')->andReturn(7);
        $action->shouldReceive('resource')->andReturn('/admin/categories');

        $html = $action->html();

        $this->assertStringContainsString('/admin/categories/7/edit', $html);
    }

    public function test_html_contains_edit_icon(): void
    {
        $action = Mockery::mock(Edit::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $action->shouldReceive('getKey')->andReturn(1);
        $action->shouldReceive('resource')->andReturn('/admin/categories');

        $html = $action->html();

        $this->assertStringContainsString('icon-edit-1', $html);
    }
}
