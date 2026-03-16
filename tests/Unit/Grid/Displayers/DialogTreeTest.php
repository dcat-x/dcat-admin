<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Displayers;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Column;
use Dcat\Admin\Grid\Displayers\AbstractDisplayer;
use Dcat\Admin\Grid\Displayers\DialogTree;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class DialogTreeTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function makeDisplayer($value = [1, 2]): DialogTree
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('getKeyName')->andReturn('id');
        $grid->shouldReceive('resource')->andReturn('/admin/roles');

        $column = Mockery::mock(Column::class);
        $column->shouldReceive('getName')->andReturn('roles');
        $column->shouldReceive('getLabel')->andReturn('Roles');

        return new class($value, $grid, $column, ['id' => 1]) extends DialogTree
        {
            public function exposeFormat($val): string
            {
                return $this->format($val);
            }
        };
    }

    public function test_dialog_tree_is_displayer(): void
    {
        $displayer = $this->makeDisplayer();

        $this->assertInstanceOf(AbstractDisplayer::class, $displayer);
    }

    public function test_nodes_options_and_columns_are_applied_to_display_output(): void
    {
        $displayer = $this->makeDisplayer([1, 2]);
        $html = $displayer
            ->nodes([
                ['node_id' => 1, 'title' => 'Dashboard', 'pid' => 0],
                ['node_id' => 2, 'title' => 'Users', 'pid' => 1],
            ])
            ->setIdColumn('node_id')
            ->setTitleColumn('title')
            ->setParentColumn('pid')
            ->title('Pick Menus')
            ->area('500px', '420px')
            ->rootParentId(0)
            ->options(['checkbox' => ['tie_selection' => false]])
            ->url('api/tree')
            ->checkAll()
            ->display();

        $this->assertStringContainsString('class="grid-dialog-tree"', $html);
        $this->assertStringContainsString('data-title="Pick Menus"', $html);
        $this->assertStringContainsString('data-url="', $html);
        $this->assertStringContainsString('api/tree', $html);
        $this->assertStringContainsString('data-checked="1"', $html);
        $this->assertStringContainsString('data-val="1,2"', $html);
    }

    public function test_display_uses_column_label_as_default_title(): void
    {
        $displayer = $this->makeDisplayer([3, 5]);
        $html = $displayer->display();

        $this->assertStringContainsString('data-title="Roles"', $html);
        $this->assertStringContainsString('data-val="3,5"', $html);
    }

    public function test_display_accepts_closure_to_mutate_state(): void
    {
        $displayer = $this->makeDisplayer([9]);
        $html = $displayer->display(function (DialogTree $tree) {
            $tree->title('From closure')->nodes([['id' => 9, 'name' => 'Settings', 'parent_id' => 0]]);
        });

        $this->assertStringContainsString('data-title="From closure"', $html);
    }

    public function test_display_accepts_arrayable_nodes(): void
    {
        $displayer = $this->makeDisplayer([7]);
        $html = $displayer->display(collect([['id' => 7, 'name' => 'Logs', 'parent_id' => 0]]));

        $this->assertStringContainsString('class="grid-dialog-tree"', $html);
        $this->assertStringContainsString('data-val="7"', $html);
    }

    public function test_format_flattens_value_array_to_csv_string(): void
    {
        $displayer = $this->makeDisplayer();

        $this->assertSame('4,6', $displayer->exposeFormat([4, 6]));
        $this->assertSame('', $displayer->exposeFormat([]));
    }
}
