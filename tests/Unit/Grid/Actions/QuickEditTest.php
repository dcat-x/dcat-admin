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

    public function test_is_instance_of_row_action(): void
    {
        $action = new QuickEdit;

        $this->assertInstanceOf(RowAction::class, $action);
    }

    public function test_title_contains_edit_icon_by_default(): void
    {
        $action = new QuickEdit;
        $title = $action->title();

        $this->assertStringContainsString('icon-edit', $title);
    }

    public function test_title_returns_custom_when_set(): void
    {
        $action = new QuickEdit;

        $ref = new \ReflectionProperty($action, 'title');
        $ref->setAccessible(true);
        $ref->setValue($action, 'Custom Quick Edit');

        $this->assertSame('Custom Quick Edit', $action->title());
    }

    public function test_make_selector_returns_quick_edit(): void
    {
        $action = new QuickEdit;

        $this->assertSame('quick-edit', $action->makeSelector());
    }

    public function test_render_sets_data_url_attribute(): void
    {
        $action = new QuickEdit;

        $grid = Mockery::mock(\Dcat\Admin\Grid::class);
        $grid->shouldReceive('option')->with('dialog_form_area')->andReturn([900, 600]);
        $grid->shouldReceive('getEditUrl')->with(8)->andReturn('/admin/users/8/edit');
        $grid->shouldReceive('getKeyName')->andReturn('id');

        $action->setGrid($grid)->setRow((object) ['id' => 8]);

        $html = $action->render();

        $this->assertStringContainsString('data-url="/admin/users/8/edit"', $html);
    }

    public function test_public_method_signatures_are_expected(): void
    {
        $title = new \ReflectionMethod(QuickEdit::class, 'title');
        $makeSelector = new \ReflectionMethod(QuickEdit::class, 'makeSelector');
        $render = new \ReflectionMethod(QuickEdit::class, 'render');

        $this->assertTrue($title->isPublic());
        $this->assertCount(0, $title->getParameters());

        $this->assertTrue($makeSelector->isPublic());
        $this->assertCount(0, $makeSelector->getParameters());

        $this->assertTrue($render->isPublic());
        $this->assertCount(0, $render->getParameters());
    }
}
