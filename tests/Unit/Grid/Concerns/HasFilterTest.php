<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Concerns;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Concerns\HasFilter;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Support\Collection;
use Mockery;

class HasFilterTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_filter_getter_returns_current_filter_instance(): void
    {
        $grid = new HasFilterTestHelper;
        $filter = new FakeGridFilter;
        $grid->setFilter($filter);

        $this->assertSame($filter, $grid->filter());
    }

    public function test_filter_callback_is_invoked_and_returns_self(): void
    {
        $grid = new HasFilterTestHelper;
        $filter = new FakeGridFilter;
        $grid->setFilter($filter);

        $called = false;

        $result = $grid->filter(function ($passedFilter) use (&$called, $filter) {
            $called = true;
            $this->assertSame($filter, $passedFilter);
        });

        $this->assertTrue($called);
        $this->assertSame($grid, $result);
    }

    public function test_render_filter_returns_empty_string_when_disabled(): void
    {
        $grid = new HasFilterTestHelper;
        $grid->setFilter(new FakeGridFilter);
        $grid->option('filter', false);

        $this->assertSame('', $grid->renderFilter());
    }

    public function test_render_filter_delegates_to_filter_render_when_enabled(): void
    {
        $grid = new HasFilterTestHelper;
        $filter = new FakeGridFilter;
        $filter->renderedValue = '<div>filter</div>';
        $grid->setFilter($filter);
        $grid->option('filter', true);

        $this->assertSame('<div>filter</div>', $grid->renderFilter());
    }

    public function test_expand_filter_calls_expand_and_returns_self(): void
    {
        $grid = new HasFilterTestHelper;
        $filter = new FakeGridFilter;
        $grid->setFilter($filter);

        $result = $grid->expandFilter();

        $this->assertSame($grid, $result);
        $this->assertTrue($filter->expanded);
    }

    public function test_disable_filter_updates_option_and_filter_state(): void
    {
        $grid = new HasFilterTestHelper;
        $filter = new FakeGridFilter;
        $grid->setFilter($filter);

        $result = $grid->disableFilter();

        $this->assertSame($grid, $result);
        $this->assertTrue($filter->disableCollapseCalledWith);
        $this->assertFalse($grid->option('filter'));
    }

    public function test_show_filter_enables_filter_option(): void
    {
        $grid = new HasFilterTestHelper;
        $filter = new FakeGridFilter;
        $grid->setFilter($filter);

        $result = $grid->showFilter();

        $this->assertSame($grid, $result);
        $this->assertFalse($filter->disableCollapseCalledWith);
        $this->assertTrue($grid->option('filter'));
    }

    public function test_disable_filter_button_and_show_filter_button_delegate_to_tools(): void
    {
        $grid = new HasFilterTestHelper;

        $disableResult = $grid->disableFilterButton();
        $showResult = $grid->showFilterButton();

        $this->assertSame($grid, $disableResult);
        $this->assertSame($grid, $showResult);
        $this->assertSame([true, false], $grid->toolCalls);
    }

    public function test_process_filter_runs_pipeline_and_returns_collection(): void
    {
        $grid = new HasFilterTestHelper;
        $filter = new FakeGridFilter;
        $grid->setFilter($filter);

        $result = $grid->processFilter();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame(['done'], $result->all());
        $this->assertSame(
            ['callBuilder', 'handleExportRequest', 'applyQuickSearch', 'applyColumnFilter', 'applySelectorQuery'],
            $grid->calls
        );
    }
}

class HasFilterTestHelper extends Grid
{
    use HasFilter;

    public array $calls = [];

    public array $toolCalls = [];

    public function __construct()
    {
        $this->options = ['filter' => true];
        $this->tools = new class($this)
        {
            public function __construct(private HasFilterTestHelper $grid) {}

            public function disableFilterButton(bool $disable): void
            {
                $this->grid->toolCalls[] = $disable;
            }
        };
    }

    public function setFilter(FakeGridFilter $filter): void
    {
        $this->filter = $filter;
    }

    protected function invokeFilterBuilder(\Closure $callback): void
    {
        $callback($this->filter);
    }

    public function callBuilder()
    {
        $this->calls[] = 'callBuilder';
    }

    public function handleExportRequest($forceExport = false)
    {
        $this->calls[] = 'handleExportRequest';
    }

    public function applyQuickSearch()
    {
        $this->calls[] = 'applyQuickSearch';
    }

    protected function applyColumnFilter()
    {
        $this->calls[] = 'applyColumnFilter';
    }

    protected function applySelectorQuery()
    {
        $this->calls[] = 'applySelectorQuery';
    }
}

class FakeGridFilter
{
    public bool $expanded = false;

    public ?bool $disableCollapseCalledWith = null;

    public string $renderedValue = '';

    public function render(): string
    {
        return $this->renderedValue;
    }

    public function expand(): void
    {
        $this->expanded = true;
    }

    public function disableCollapse(bool $disable): void
    {
        $this->disableCollapseCalledWith = $disable;
    }

    public function execute(): Collection
    {
        return collect(['done']);
    }
}
