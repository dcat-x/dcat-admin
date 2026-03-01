<?php

namespace Dcat\Admin\Tests\Unit\Grid\Events;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Events\ApplyFilter;
use Dcat\Admin\Grid\Events\ApplyQuickSearch;
use Dcat\Admin\Grid\Events\ApplySelector;
use Dcat\Admin\Grid\Events\Event;
use Dcat\Admin\Grid\Events\Exporting;
use Dcat\Admin\Grid\Events\Fetched;
use Dcat\Admin\Grid\Events\Fetching;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class GridEventTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function createMockGrid(): Grid
    {
        return Mockery::mock(Grid::class);
    }

    public function test_apply_filter_event_can_be_instantiated(): void
    {
        $event = new ApplyFilter;

        $this->assertInstanceOf(ApplyFilter::class, $event);
        $this->assertInstanceOf(Event::class, $event);
    }

    public function test_apply_quick_search_event_can_be_instantiated(): void
    {
        $event = new ApplyQuickSearch;

        $this->assertInstanceOf(ApplyQuickSearch::class, $event);
        $this->assertInstanceOf(Event::class, $event);
    }

    public function test_apply_selector_event_can_be_instantiated(): void
    {
        $event = new ApplySelector;

        $this->assertInstanceOf(ApplySelector::class, $event);
        $this->assertInstanceOf(Event::class, $event);
    }

    public function test_exporting_event_can_be_instantiated(): void
    {
        $event = new Exporting;

        $this->assertInstanceOf(Exporting::class, $event);
        $this->assertInstanceOf(Event::class, $event);
    }

    public function test_fetched_event_can_be_instantiated(): void
    {
        $event = new Fetched;

        $this->assertInstanceOf(Fetched::class, $event);
        $this->assertInstanceOf(Event::class, $event);
    }

    public function test_fetching_event_can_be_instantiated(): void
    {
        $event = new Fetching;

        $this->assertInstanceOf(Fetching::class, $event);
        $this->assertInstanceOf(Event::class, $event);
    }

    public function test_constructor_sets_default_empty_payload(): void
    {
        $event = new Fetching;

        $this->assertIsArray($event->payload);
        $this->assertEmpty($event->payload);
    }

    public function test_constructor_sets_custom_payload(): void
    {
        $payload = ['key' => 'value', 'filter' => 'name'];
        $event = new Fetching($payload);

        $this->assertSame($payload, $event->payload);
    }

    public function test_grid_is_null_by_default(): void
    {
        $event = new Fetching;

        $this->assertNull($event->grid);
    }

    public function test_set_grid_assigns_grid_instance(): void
    {
        $grid = $this->createMockGrid();
        $event = new Fetching;

        $event->setGrid($grid);

        $this->assertSame($grid, $event->grid);
    }

    public function test_each_event_can_receive_payload(): void
    {
        $payload = ['action' => 'test', 'data' => [1, 2, 3]];
        $eventClasses = [
            ApplyFilter::class,
            ApplyQuickSearch::class,
            ApplySelector::class,
            Exporting::class,
            Fetched::class,
            Fetching::class,
        ];

        foreach ($eventClasses as $eventClass) {
            $event = new $eventClass($payload);
            $this->assertSame($payload, $event->payload, "Failed asserting payload is set for {$eventClass}");
        }
    }

    public function test_each_event_has_default_empty_payload(): void
    {
        $eventClasses = [
            ApplyFilter::class,
            ApplyQuickSearch::class,
            ApplySelector::class,
            Exporting::class,
            Fetched::class,
            Fetching::class,
        ];

        foreach ($eventClasses as $eventClass) {
            $event = new $eventClass;
            $this->assertIsArray($event->payload, "Failed asserting payload is array for {$eventClass}");
            $this->assertEmpty($event->payload, "Failed asserting payload is empty for {$eventClass}");
        }
    }

    public function test_each_event_supports_set_grid(): void
    {
        $grid = $this->createMockGrid();
        $eventClasses = [
            ApplyFilter::class,
            ApplyQuickSearch::class,
            ApplySelector::class,
            Exporting::class,
            Fetched::class,
            Fetching::class,
        ];

        foreach ($eventClasses as $eventClass) {
            $event = new $eventClass;
            $event->setGrid($grid);
            $this->assertSame($grid, $event->grid, "Failed asserting grid is set for {$eventClass}");
        }
    }

    public function test_set_grid_can_replace_grid(): void
    {
        $grid1 = $this->createMockGrid();
        $grid2 = $this->createMockGrid();

        $event = new Fetching;
        $event->setGrid($grid1);
        $this->assertSame($grid1, $event->grid);

        $event->setGrid($grid2);
        $this->assertSame($grid2, $event->grid);
    }
}
