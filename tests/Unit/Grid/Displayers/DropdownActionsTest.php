<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Displayers;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Column;
use Dcat\Admin\Grid\Displayers\Actions;
use Dcat\Admin\Grid\Displayers\DropdownActions;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class DropdownActionsTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function makeDisplayer(array $options = []): DropdownActions
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
        $grid->shouldReceive('getRowName')->andReturn('grid-row');
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

        return new class('Taylor', $grid, $column, ['id' => 7]) extends DropdownActions
        {
            public function exposeViewLabel(): string
            {
                return $this->getViewLabel();
            }

            public function exposeEditLabel(): string
            {
                return $this->getEditLabel();
            }

            public function exposeQuickEditLabel(): string
            {
                return $this->getQuickEditLabel();
            }

            public function exposeDeleteLabel(): string
            {
                return $this->getDeleteLabel();
            }
        };
    }

    public function test_dropdown_actions_is_actions_subclass_instance(): void
    {
        $displayer = $this->makeDisplayer();

        $this->assertInstanceOf(Actions::class, $displayer);
    }

    public function test_display_returns_view_with_default_actions_and_selector(): void
    {
        $displayer = $this->makeDisplayer();
        $view = $displayer->display();

        $this->assertSame('admin::grid.dropdown-actions', $view->name());
        $this->assertSame('.grid-row-checkbox', $view->getData()['selector']);
        $this->assertCount(3, $view->getData()['default']);
    }

    public function test_prepend_behaves_like_append_and_wraps_plain_text_action(): void
    {
        $displayer = $this->makeDisplayer([
            'view_button' => false,
            'edit_button' => false,
            'quick_edit_button' => false,
            'delete_button' => false,
        ]);

        $displayer->prepend('Custom Action');

        $view = $displayer->display();
        $custom = $view->getData()['custom'];

        $this->assertCount(1, $custom);
        $this->assertSame('<a>Custom Action</a>', $custom[0]);
    }

    public function test_prepend_keeps_existing_anchor_action(): void
    {
        $displayer = $this->makeDisplayer([
            'view_button' => false,
            'edit_button' => false,
            'quick_edit_button' => false,
            'delete_button' => false,
        ]);

        $displayer->prepend('<a href="/x">Link</a>');

        $view = $displayer->display();
        $custom = $view->getData()['custom'];

        $this->assertSame('<a href="/x">Link</a>', $custom[0]);
    }

    public function test_label_methods_return_empty_strings(): void
    {
        $displayer = $this->makeDisplayer();

        $this->assertSame('', $displayer->exposeViewLabel());
        $this->assertSame('', $displayer->exposeEditLabel());
        $this->assertSame('', $displayer->exposeQuickEditLabel());
        $this->assertSame('', $displayer->exposeDeleteLabel());
    }
}
