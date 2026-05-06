<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\GridAction;
use Dcat\Admin\Grid\Tools;
use Dcat\Admin\Grid\Tools\BatchActions;
use Dcat\Admin\Grid\Tools\FilterButton;
use Dcat\Admin\Grid\Tools\RefreshButton;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Support\Collection;
use Mockery;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

#[AllowMockObjectsWithoutExpectations]
class ToolsTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function createMockGrid(): Grid
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('option')->andReturn(null);

        return $grid;
    }

    public function test_constructor_creates_tools_instance(): void
    {
        $grid = $this->createMockGrid();
        $tools = new Tools($grid);

        $this->assertInstanceOf(Tools::class, $tools);
    }

    public function test_has_returns_true_when_default_tools_exist(): void
    {
        $grid = $this->createMockGrid();
        $tools = new Tools($grid);

        $this->assertTrue($tools->has());
    }

    public function test_default_tools_include_at_least_three_items(): void
    {
        $grid = $this->createMockGrid();
        $tools = new Tools($grid);

        $reflection = new \ReflectionClass($tools);
        $property = $reflection->getProperty('tools');
        $property->setAccessible(true);

        /** @var Collection $collection */
        $collection = $property->getValue($tools);

        $this->assertGreaterThanOrEqual(3, $collection->count());
    }

    public function test_default_tools_contain_batch_actions_refresh_and_filter(): void
    {
        $grid = $this->createMockGrid();
        $tools = new Tools($grid);

        $reflection = new \ReflectionClass($tools);
        $property = $reflection->getProperty('tools');
        $property->setAccessible(true);

        /** @var Collection $collection */
        $collection = $property->getValue($tools);

        $hasBatchActions = $collection->contains(fn ($tool) => $tool instanceof BatchActions);
        $hasRefreshButton = $collection->contains(fn ($tool) => $tool instanceof RefreshButton);
        $hasFilterButton = $collection->contains(fn ($tool) => $tool instanceof FilterButton);

        $this->assertTrue($hasBatchActions, 'Default tools should contain BatchActions');
        $this->assertTrue($hasRefreshButton, 'Default tools should contain RefreshButton');
        $this->assertTrue($hasFilterButton, 'Default tools should contain FilterButton');
    }

    public function test_append_returns_this_for_fluent_api(): void
    {
        $grid = $this->createMockGrid();
        $tools = new Tools($grid);

        $result = $tools->append('custom tool');

        $this->assertSame($tools, $result);
    }

    public function test_append_adds_tool_to_collection(): void
    {
        $grid = $this->createMockGrid();
        $tools = new Tools($grid);

        $reflection = new \ReflectionClass($tools);
        $property = $reflection->getProperty('tools');
        $property->setAccessible(true);

        $countBefore = $property->getValue($tools)->count();
        $tools->append('appended tool');
        $countAfter = $property->getValue($tools)->count();

        $this->assertSame($countBefore + 1, $countAfter);
    }

    public function test_append_with_string_tool(): void
    {
        $grid = $this->createMockGrid();
        $tools = new Tools($grid);

        $tools->append('custom string tool');

        $reflection = new \ReflectionClass($tools);
        $property = $reflection->getProperty('tools');
        $property->setAccessible(true);

        /** @var Collection $collection */
        $collection = $property->getValue($tools);

        $this->assertSame('custom string tool', $collection->last());
    }

    public function test_prepend_returns_this_for_fluent_api(): void
    {
        $grid = $this->createMockGrid();
        $tools = new Tools($grid);

        $result = $tools->prepend('prepended tool');

        $this->assertSame($tools, $result);
    }

    public function test_prepend_adds_tool_at_beginning(): void
    {
        $grid = $this->createMockGrid();
        $tools = new Tools($grid);

        $tools->prepend('first tool');

        $reflection = new \ReflectionClass($tools);
        $property = $reflection->getProperty('tools');
        $property->setAccessible(true);

        /** @var Collection $collection */
        $collection = $property->getValue($tools);

        $this->assertSame('first tool', $collection->first());
    }

    public function test_prepend_with_string_tool(): void
    {
        $grid = $this->createMockGrid();
        $tools = new Tools($grid);

        $tools->prepend('prepended string');

        $reflection = new \ReflectionClass($tools);
        $property = $reflection->getProperty('tools');
        $property->setAccessible(true);

        /** @var Collection $collection */
        $collection = $property->getValue($tools);

        $this->assertSame('prepended string', $collection->first());
    }

    public function test_with_outline_sets_value_and_returns_this(): void
    {
        $grid = $this->createMockGrid();
        $tools = new Tools($grid);

        $result = $tools->withOutline(false);

        $this->assertSame($tools, $result);

        $reflection = new \ReflectionClass($tools);
        $property = $reflection->getProperty('outline');
        $property->setAccessible(true);

        $this->assertFalse($property->getValue($tools));
    }

    public function test_append_with_grid_action_calls_set_grid(): void
    {
        $grid = $this->createMockGrid();
        $tools = new Tools($grid);

        $action = Mockery::mock(GridAction::class);
        $action->shouldReceive('setGrid')->with($grid)->once();

        $tools->append($action);

        $this->assertTrue(true); // Mockery verifies the expectation in tearDown
    }
}
