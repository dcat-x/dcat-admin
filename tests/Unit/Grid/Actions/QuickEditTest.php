<?php

namespace Dcat\Admin\Tests\Unit\Grid\Actions;

use Dcat\Admin\Grid\Actions\QuickEdit;
use Dcat\Admin\Grid\RowAction;
use Dcat\Admin\Tests\TestCase;
use Mockery;

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

    public function test_has_title_method(): void
    {
        $this->assertTrue(method_exists(QuickEdit::class, 'title'));
    }

    public function test_title_contains_edit_icon(): void
    {
        $action = new QuickEdit;
        $title = $action->title();

        $this->assertStringContainsString('icon-edit', $title);
    }

    public function test_has_make_selector_method(): void
    {
        $this->assertTrue(method_exists(QuickEdit::class, 'makeSelector'));
    }

    public function test_make_selector_returns_quick_edit(): void
    {
        $action = new QuickEdit;

        $this->assertEquals('quick-edit', $action->makeSelector());
    }
}
