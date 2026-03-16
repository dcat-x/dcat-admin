<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Concerns;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Concerns\HasEvents;
use Dcat\Admin\Grid\Events\Fetching;
use Dcat\Admin\Tests\TestCase;

class HasEventsTest extends TestCase
{
    public function test_dispatched_initially_empty_array(): void
    {
        $grid = new HasEventsTestHelper;

        $reflection = new \ReflectionProperty($grid, 'dispatched');
        $reflection->setAccessible(true);

        $this->assertIsArray($reflection->getValue($grid));
        $this->assertEmpty($reflection->getValue($grid));
    }

    public function test_fire_sets_grid_on_event_and_marks_event_as_dispatched(): void
    {
        $grid = new HasEventsTestHelper;
        $event = new Fetching;

        $grid->fire($event);

        $reflection = new \ReflectionProperty($grid, 'dispatched');
        $reflection->setAccessible(true);
        $dispatched = $reflection->getValue($grid);

        $this->assertSame($grid, $event->grid);
        $this->assertContains(Fetching::class, array_keys($dispatched));
        $this->assertSame($event, $dispatched[Fetching::class]);
    }

    public function test_fire_once_dispatches_only_once_for_same_event_type(): void
    {
        $grid = new HasEventsTestHelper;

        $first = new Fetching(['a']);
        $second = new Fetching(['b']);

        $grid->fireOnce($first);
        $grid->fireOnce($second);

        $reflection = new \ReflectionProperty($grid, 'dispatched');
        $reflection->setAccessible(true);
        $dispatched = $reflection->getValue($grid);

        $this->assertCount(1, $dispatched);
        $this->assertSame($first, $dispatched[Fetching::class]);
    }

    public function test_method_signatures_are_expected(): void
    {
        $listen = new \ReflectionMethod(HasEventsTestHelper::class, 'listen');
        $fire = new \ReflectionMethod(HasEventsTestHelper::class, 'fire');
        $fireOnce = new \ReflectionMethod(HasEventsTestHelper::class, 'fireOnce');

        $this->assertTrue($listen->isPublic());
        $this->assertCount(2, $listen->getParameters());

        $this->assertTrue($fire->isPublic());
        $this->assertCount(1, $fire->getParameters());

        $this->assertTrue($fireOnce->isPublic());
        $this->assertCount(1, $fireOnce->getParameters());
    }
}

class HasEventsTestHelper extends Grid
{
    use HasEvents;

    public function __construct()
    {
        // Skip parent constructor
    }
}
