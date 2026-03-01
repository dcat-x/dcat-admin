<?php

namespace Dcat\Admin\Tests\Unit\Tree\Actions;

use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Tree\Actions\Delete;
use Dcat\Admin\Tree\RowAction;
use Mockery;

class DeleteTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function makeDeleteAction($key = 1): Delete
    {
        $action = Mockery::mock(Delete::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $action->shouldReceive('getKey')->andReturn($key);
        $action->shouldReceive('resource')->andReturn('/admin/categories');

        return $action;
    }

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(Delete::class));
    }

    public function test_extends_row_action(): void
    {
        $action = Mockery::mock(Delete::class)->makePartial();

        $this->assertInstanceOf(RowAction::class, $action);
    }

    public function test_has_html_method(): void
    {
        $this->assertTrue(method_exists(Delete::class, 'html'));
    }

    public function test_html_method_returns_string(): void
    {
        $action = $this->makeDeleteAction(5);

        $html = $action->html();

        $this->assertIsString($html);
    }

    public function test_html_contains_data_action_delete(): void
    {
        $action = $this->makeDeleteAction(10);

        $html = $action->html();

        $this->assertStringContainsString('data-action="delete"', $html);
    }

    public function test_html_contains_icon_trash(): void
    {
        $action = $this->makeDeleteAction(1);

        $html = $action->html();

        $this->assertStringContainsString('icon-trash', $html);
    }

    public function test_html_contains_data_message_with_key(): void
    {
        $action = $this->makeDeleteAction(42);

        $html = $action->html();

        $this->assertStringContainsString('data-message="ID - 42"', $html);
    }
}
