<?php

namespace Dcat\Admin\Tests\Unit\Grid\Actions;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Column;
use Dcat\Admin\Grid\Displayers\Actions;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Support\Fluent;
use Mockery;

class RowActionTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function createActions(): Actions
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('resource')->andReturn('/admin/users');
        $grid->shouldReceive('getKeyName')->andReturn('id');

        $column = Mockery::mock(Column::class);
        $column->shouldReceive('getName')->andReturn('actions');

        $row = new Fluent(['id' => 1, 'name' => 'Test']);

        return new Actions(null, $grid, $column, $row);
    }

    public function test_default_action_states(): void
    {
        $actions = $this->createActions();
        $ref = new \ReflectionProperty($actions, 'actions');
        $ref->setAccessible(true);
        $states = $ref->getValue($actions);

        $this->assertTrue($states['view']);
        $this->assertTrue($states['edit']);
        $this->assertFalse($states['quickEdit']);
        $this->assertTrue($states['delete']);
    }

    public function test_disable_view(): void
    {
        $actions = $this->createActions();
        $actions->disableView();

        $ref = new \ReflectionProperty($actions, 'actions');
        $ref->setAccessible(true);
        $this->assertFalse($ref->getValue($actions)['view']);
    }

    public function test_disable_edit(): void
    {
        $actions = $this->createActions();
        $actions->disableEdit();

        $ref = new \ReflectionProperty($actions, 'actions');
        $ref->setAccessible(true);
        $this->assertFalse($ref->getValue($actions)['edit']);
    }

    public function test_disable_delete(): void
    {
        $actions = $this->createActions();
        $actions->disableDelete();

        $ref = new \ReflectionProperty($actions, 'actions');
        $ref->setAccessible(true);
        $this->assertFalse($ref->getValue($actions)['delete']);
    }

    public function test_enable_quick_edit(): void
    {
        $actions = $this->createActions();
        $actions->quickEdit();

        $ref = new \ReflectionProperty($actions, 'actions');
        $ref->setAccessible(true);
        $this->assertTrue($ref->getValue($actions)['quickEdit']);
    }

    public function test_disable_quick_edit(): void
    {
        $actions = $this->createActions();
        $actions->quickEdit();
        $actions->disableQuickEdit();

        $ref = new \ReflectionProperty($actions, 'actions');
        $ref->setAccessible(true);
        $this->assertFalse($ref->getValue($actions)['quickEdit']);
    }

    public function test_view_enables_action(): void
    {
        $actions = $this->createActions();
        $actions->disableView();
        $actions->view();

        $ref = new \ReflectionProperty($actions, 'actions');
        $ref->setAccessible(true);
        $this->assertTrue($ref->getValue($actions)['view']);
    }

    public function test_set_resource(): void
    {
        $actions = $this->createActions();
        $result = $actions->setResource('/admin/posts');

        $this->assertSame($actions, $result);
        $this->assertEquals('/admin/posts', $actions->resource());
    }

    public function test_append_returns_this(): void
    {
        $actions = $this->createActions();
        $result = $actions->append('custom html');
        $this->assertSame($actions, $result);
    }

    public function test_prepend_returns_this(): void
    {
        $actions = $this->createActions();
        $result = $actions->prepend('custom html');
        $this->assertSame($actions, $result);
    }

    public function test_add_returns_static(): void
    {
        $actions = $this->createActions();
        $result = $actions->add('custom action');
        $this->assertSame($actions, $result);
    }

    public function test_set_edit_text_returns_static(): void
    {
        $actions = $this->createActions();
        $result = $actions->setEditText('Edit');
        $this->assertSame($actions, $result);
    }

    public function test_set_view_icon_returns_static(): void
    {
        $actions = $this->createActions();
        $result = $actions->setViewIcon('search');
        $this->assertSame($actions, $result);
    }

    public function test_set_delete_text_and_icon(): void
    {
        $actions = $this->createActions();
        $actions->setDeleteText('Remove');
        $actions->setDeleteIcon('trash-2');

        $ref1 = new \ReflectionProperty($actions, 'deleteText');
        $ref1->setAccessible(true);
        $ref2 = new \ReflectionProperty($actions, 'deleteIcon');
        $ref2->setAccessible(true);

        $this->assertEquals('Remove', $ref1->getValue($actions));
        $this->assertEquals('trash-2', $ref2->getValue($actions));
    }
}
