<?php

namespace Dcat\Admin\Tests\Unit\Grid\Displayers;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Column;
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

    protected function makeDisplayer(array $options = []): Actions
    {
        $options = array_merge([
            'view_button' => true,
            'edit_button' => true,
            'quick_edit_button' => false,
            'delete_button' => true,
        ], $options);

        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('option')->with('view_button')->andReturn($options['view_button']);
        $grid->shouldReceive('option')->with('edit_button')->andReturn($options['edit_button']);
        $grid->shouldReceive('option')->with('quick_edit_button')->andReturn($options['quick_edit_button']);
        $grid->shouldReceive('option')->with('delete_button')->andReturn($options['delete_button']);
        $grid->shouldReceive('option')->with('dialog_form_area')->andReturn([900, 640]);
        $grid->shouldReceive('resource')->andReturn('/admin/users');
        $grid->shouldReceive('getKeyName')->andReturn('id');
        $grid->shouldReceive('urlWithConstraints')->andReturnUsing(fn ($url) => $url);
        $grid->shouldReceive('getEditUrl')->andReturnUsing(fn ($id) => "/admin/users/{$id}/edit");
        $grid->shouldReceive('model')->andReturn(new class
        {
            public function withoutTreeQuery(string $url): string
            {
                return $url;
            }
        });

        $column = Mockery::mock(Column::class);
        $column->shouldReceive('getName')->andReturn('name');

        return new Actions('Taylor', $grid, $column, ['id' => 1, 'name' => 'Taylor']);
    }

    public function test_actions_is_displayer_and_uses_macroable(): void
    {
        $actions = $this->makeDisplayer();

        $this->assertInstanceOf(AbstractDisplayer::class, $actions);
        $this->assertContains(Macroable::class, class_uses(Actions::class));
    }

    public function test_display_renders_default_enabled_actions(): void
    {
        $actions = $this->makeDisplayer();
        $html = $actions->display();

        $this->assertStringContainsString('icon-eye', $html);
        $this->assertStringContainsString('icon-edit-1', $html);
        $this->assertStringContainsString('icon-trash', $html);
        $this->assertStringNotContainsString('icon-edit grid-action-icon', $html);
    }

    public function test_display_can_disable_all_default_actions_via_callback(): void
    {
        $actions = $this->makeDisplayer();

        $html = $actions->display([
            function (Actions $actions) {
                $actions
                    ->disableView()
                    ->disableEdit()
                    ->disableQuickEdit()
                    ->disableDelete();
            },
        ]);

        $this->assertSame('', $html);
    }

    public function test_display_renders_prepend_custom_and_append_in_order(): void
    {
        $actions = $this->makeDisplayer([
            'view_button' => false,
            'edit_button' => false,
            'quick_edit_button' => false,
            'delete_button' => false,
        ]);

        $actions->prepend('<span>prepend</span>');
        $actions->add('<span>custom</span>');
        $actions->append('<span>append</span>');

        $this->assertSame(
            '<span>prepend</span><span>custom</span><span>append</span>',
            $actions->display()
        );
    }

    public function test_setters_customize_labels_and_icons(): void
    {
        $actions = $this->makeDisplayer([
            'view_button' => false,
            'edit_button' => false,
            'quick_edit_button' => false,
            'delete_button' => false,
        ]);

        $html = $actions->display([
            function (Actions $actions) {
                $actions
                    ->setViewText('View It')
                    ->setViewIcon('search')
                    ->setEditText('Edit It')
                    ->setEditIcon('edit')
                    ->setQuickEditText('Quick Edit')
                    ->setQuickEditIcon('zap')
                    ->setDeleteText('Delete It')
                    ->setDeleteIcon('x')
                    ->view()
                    ->edit()
                    ->quickEdit()
                    ->delete();
            },
        ]);

        $this->assertStringContainsString('View It', $html);
        $this->assertStringContainsString('Edit It', $html);
        $this->assertStringContainsString('Quick Edit', $html);
        $this->assertStringContainsString('Delete It', $html);
        $this->assertStringContainsString('icon-search', $html);
        $this->assertStringContainsString('icon-edit', $html);
        $this->assertStringContainsString('icon-zap', $html);
        $this->assertStringContainsString('icon-x', $html);
    }

    public function test_set_resource_overrides_grid_resource(): void
    {
        $actions = $this->makeDisplayer();

        $this->assertSame('/admin/users', $actions->resource());

        $actions->setResource('/admin/custom-users');

        $this->assertSame('/admin/custom-users', $actions->resource());
    }

    public function test_macroable_allows_registering_runtime_instance_method(): void
    {
        Actions::macro('fromMacro', function () {
            return 'macro-ok';
        });

        $actions = $this->makeDisplayer();

        $this->assertSame('macro-ok', $actions->fromMacro());

        Actions::flushMacros();
    }
}
