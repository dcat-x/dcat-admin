<?php

namespace Dcat\Admin\Tests\Unit\Grid;

use Dcat\Admin\Grid\Filter\DateRange;
use Dcat\Admin\Grid\Filter\Presenter\DateRangeQuick;
use Dcat\Admin\Grid\Filter\Presenter\Toggle as TogglePresenter;
use Dcat\Admin\Grid\Filter\Toggle;
use Dcat\Admin\Grid\Filter\WhereNotNull;
use Dcat\Admin\Grid\Filter\WhereNull;
use Dcat\Admin\Tests\TestCase;

class FilterTest extends TestCase
{
    public function test_where_null_filter_query(): void
    {
        $filter = new WhereNull('deleted_at', 'Deleted');
        $this->assertEquals('deleted_at', $filter->originalColumn());
        $this->assertEquals('Deleted', $filter->getLabel());
    }

    public function test_where_null_condition_with_value(): void
    {
        $filter = new WhereNull('deleted_at', 'Deleted');

        // Mock parent and grid
        $grid = $this->createMock(\Dcat\Admin\Grid::class);
        $grid->method('makeName')->willReturnCallback(function ($name) {
            return $name;
        });

        $parentFilter = $this->createMock(\Dcat\Admin\Grid\Filter::class);
        $parentFilter->method('grid')->willReturn($grid);

        $filter->setParent($parentFilter);

        $condition = $filter->condition(['deleted_at' => '1']);

        $this->assertIsArray($condition);
        $this->assertArrayHasKey('whereNull', $condition);
    }

    public function test_where_null_condition_without_value(): void
    {
        $filter = new WhereNull('deleted_at', 'Deleted');

        $condition = $filter->condition(['deleted_at' => '']);

        $this->assertNull($condition);
    }

    public function test_where_not_null_filter_query(): void
    {
        $filter = new WhereNotNull('email', 'Has Email');
        $this->assertEquals('email', $filter->originalColumn());
        $this->assertEquals('Has Email', $filter->getLabel());
    }

    public function test_where_not_null_condition_with_value(): void
    {
        $filter = new WhereNotNull('email', 'Has Email');

        $grid = $this->createMock(\Dcat\Admin\Grid::class);
        $grid->method('makeName')->willReturnCallback(function ($name) {
            return $name;
        });

        $parentFilter = $this->createMock(\Dcat\Admin\Grid\Filter::class);
        $parentFilter->method('grid')->willReturn($grid);

        $filter->setParent($parentFilter);

        $condition = $filter->condition(['email' => '1']);

        $this->assertIsArray($condition);
        $this->assertArrayHasKey('whereNotNull', $condition);
    }

    public function test_date_range_filter(): void
    {
        $filter = new DateRange('created_at', 'Created');
        $this->assertEquals('created_at', $filter->originalColumn());
        $this->assertEquals('Created', $filter->getLabel());
    }

    public function test_date_range_quick_presenter(): void
    {
        $presenter = new DateRangeQuick;
        $variables = $presenter->defaultVariables();

        $this->assertArrayHasKey('ranges', $variables);
        $this->assertArrayHasKey('dateOptions', $variables);
        $this->assertArrayHasKey('showDateInputs', $variables);
        $this->assertTrue($variables['showDateInputs']);
    }

    public function test_date_range_quick_custom_ranges(): void
    {
        $customRanges = [
            'Custom Range' => ['2024-01-01', '2024-12-31'],
        ];
        $presenter = new DateRangeQuick($customRanges);
        $variables = $presenter->defaultVariables();

        $this->assertEquals($customRanges, $variables['ranges']);
    }

    public function test_date_range_quick_hide_inputs(): void
    {
        $presenter = new DateRangeQuick;
        $presenter->hideDateInputs();
        $variables = $presenter->defaultVariables();

        $this->assertFalse($variables['showDateInputs']);
    }

    public function test_date_range_quick_format(): void
    {
        $presenter = new DateRangeQuick;
        $presenter->format('YYYY-MM-DD HH:mm:ss');
        $variables = $presenter->defaultVariables();

        $this->assertEquals('YYYY-MM-DD HH:mm:ss', $variables['dateOptions']['format']);
    }

    public function test_date_range_condition_start_only(): void
    {
        $filter = new DateRange('created_at', 'Created');

        $grid = $this->createMock(\Dcat\Admin\Grid::class);
        $grid->method('makeName')->willReturnCallback(function ($name) {
            return $name;
        });

        $parentFilter = $this->createMock(\Dcat\Admin\Grid\Filter::class);
        $parentFilter->method('grid')->willReturn($grid);

        $filter->setParent($parentFilter);

        $condition = $filter->condition(['created_at' => ['start' => '2024-01-01', 'end' => '']]);

        $this->assertIsArray($condition);
        $this->assertArrayHasKey('where', $condition);
    }

    public function test_date_range_condition_end_only(): void
    {
        $filter = new DateRange('created_at', 'Created');

        $grid = $this->createMock(\Dcat\Admin\Grid::class);
        $grid->method('makeName')->willReturnCallback(function ($name) {
            return $name;
        });

        $parentFilter = $this->createMock(\Dcat\Admin\Grid\Filter::class);
        $parentFilter->method('grid')->willReturn($grid);

        $filter->setParent($parentFilter);

        $condition = $filter->condition(['created_at' => ['start' => '', 'end' => '2024-12-31']]);

        $this->assertIsArray($condition);
        $this->assertArrayHasKey('where', $condition);
    }

    public function test_date_range_condition_both(): void
    {
        $filter = new DateRange('created_at', 'Created');

        $grid = $this->createMock(\Dcat\Admin\Grid::class);
        $grid->method('makeName')->willReturnCallback(function ($name) {
            return $name;
        });

        $parentFilter = $this->createMock(\Dcat\Admin\Grid\Filter::class);
        $parentFilter->method('grid')->willReturn($grid);

        $filter->setParent($parentFilter);

        $condition = $filter->condition(['created_at' => ['start' => '2024-01-01', 'end' => '2024-12-31']]);

        $this->assertIsArray($condition);
        $this->assertArrayHasKey('whereBetween', $condition);
    }

    public function test_date_range_to_timestamp(): void
    {
        $filter = new DateRange('created_at', 'Created');
        $result = $filter->toTimestamp();

        $this->assertInstanceOf(DateRange::class, $result);
    }

    public function test_toggle_filter(): void
    {
        $filter = new Toggle('is_active', 'Active');
        $this->assertEquals('is_active', $filter->originalColumn());
        $this->assertEquals('Active', $filter->getLabel());
    }

    public function test_toggle_values(): void
    {
        $filter = new Toggle('status', 'Status');
        $result = $filter->values('active', 'inactive');

        $this->assertInstanceOf(Toggle::class, $result);
    }

    public function test_toggle_presenter(): void
    {
        $presenter = new TogglePresenter('On', 'Off');
        $variables = $presenter->defaultVariables();

        $this->assertEquals('On', $variables['onText']);
        $this->assertEquals('Off', $variables['offText']);
        $this->assertEquals(1, $variables['onValue']);
        $this->assertEquals(0, $variables['offValue']);
        $this->assertEquals('small', $variables['size']);
    }

    public function test_toggle_presenter_values(): void
    {
        $presenter = new TogglePresenter;
        $presenter->values('yes', 'no');
        $variables = $presenter->defaultVariables();

        $this->assertEquals('yes', $variables['onValue']);
        $this->assertEquals('no', $variables['offValue']);
    }

    public function test_toggle_presenter_size(): void
    {
        $presenter = new TogglePresenter;
        $presenter->size('large');
        $variables = $presenter->defaultVariables();

        $this->assertEquals('large', $variables['size']);
    }

    public function test_toggle_condition_with_value(): void
    {
        $filter = new Toggle('is_active', 'Active');

        $grid = $this->createMock(\Dcat\Admin\Grid::class);
        $grid->method('makeName')->willReturnCallback(function ($name) {
            return $name;
        });

        $parentFilter = $this->createMock(\Dcat\Admin\Grid\Filter::class);
        $parentFilter->method('grid')->willReturn($grid);

        $filter->setParent($parentFilter);

        $condition = $filter->condition(['is_active' => '1']);

        $this->assertIsArray($condition);
        $this->assertArrayHasKey('where', $condition);
    }

    public function test_toggle_condition_without_value(): void
    {
        $filter = new Toggle('is_active', 'Active');

        $condition = $filter->condition(['is_active' => '']);

        $this->assertNull($condition);
    }
}
