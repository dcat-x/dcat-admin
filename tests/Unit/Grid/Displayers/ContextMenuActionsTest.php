<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Displayers;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Column;
use Dcat\Admin\Grid\Displayers\ContextMenuActions;
use Dcat\Admin\Grid\Displayers\DropdownActions;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class ContextMenuActionsTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function makeDisplayer(): ContextMenuActions
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('getTableId')->andReturn('grid-1');
        $grid->shouldReceive('getRowName')->andReturn('grid-row');

        $column = Mockery::mock(Column::class);

        return new ContextMenuActions([], $grid, $column, ['id' => 1]);
    }

    public function test_is_instance_of_dropdown_actions(): void
    {
        $displayer = $this->makeDisplayer();

        $this->assertInstanceOf(DropdownActions::class, $displayer);
    }

    public function test_element_id_default_value(): void
    {
        $ref = new \ReflectionProperty(ContextMenuActions::class, 'elementId');
        $ref->setAccessible(true);

        $this->assertSame('grid-context-menu', $ref->getDefaultValue());
    }

    public function test_add_script_is_protected_and_parameterless(): void
    {
        $method = new \ReflectionMethod(ContextMenuActions::class, 'addScript');

        $this->assertTrue($method->isProtected());
        $this->assertCount(0, $method->getParameters());
    }

    public function test_display_is_public_and_accepts_optional_callback_parameter(): void
    {
        $method = new \ReflectionMethod(ContextMenuActions::class, 'display');
        $params = $method->getParameters();

        $this->assertTrue($method->isPublic());
        $this->assertCount(1, $params);
        $this->assertSame('callback', $params[0]->getName());
        $this->assertTrue($params[0]->isDefaultValueAvailable());
        $this->assertNull($params[0]->getDefaultValue());
    }

    public function test_display_method_is_declared_on_context_menu_actions(): void
    {
        $method = new \ReflectionMethod(ContextMenuActions::class, 'display');

        $this->assertSame(ContextMenuActions::class, $method->getDeclaringClass()->getName());
    }
}
