<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Filter;
use Dcat\Admin\Grid\Filter\DateRange;
use Dcat\Admin\Grid\Filter\Presenter\DateRangeQuick;
use Dcat\Admin\Grid\Filter\Presenter\Toggle as TogglePresenter;
use Dcat\Admin\Grid\Filter\Toggle;
use Dcat\Admin\Grid\Filter\WhereNotNull;
use Dcat\Admin\Grid\Filter\WhereNull;
use Dcat\Admin\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class FilterTest extends TestCase
{
    protected function attachParentFilter(object $filter): void
    {
        $grid = $this->createMock(Grid::class);
        $grid->method('makeName')->willReturnCallback(fn ($name) => $name);

        $parentFilter = $this->createMock(Filter::class);
        $parentFilter->method('grid')->willReturn($grid);

        $filter->setParent($parentFilter);
    }

    public function test_where_null_filter_query(): void
    {
        $filter = new WhereNull('deleted_at', 'Deleted');
        $this->assertSame('deleted_at', $filter->originalColumn());
        $this->assertSame('Deleted', $filter->getLabel());
    }

    public function test_where_null_condition_with_value(): void
    {
        $filter = new WhereNull('deleted_at', 'Deleted');

        $this->attachParentFilter($filter);

        $condition = $filter->condition(['deleted_at' => '1']);

        $this->assertConditionHasKey($condition, 'whereNull');
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
        $this->assertSame('email', $filter->originalColumn());
        $this->assertSame('Has Email', $filter->getLabel());
    }

    public function test_where_not_null_condition_with_value(): void
    {
        $filter = new WhereNotNull('email', 'Has Email');

        $this->attachParentFilter($filter);

        $condition = $filter->condition(['email' => '1']);

        $this->assertConditionHasKey($condition, 'whereNotNull');
    }

    public function test_date_range_filter(): void
    {
        $filter = new DateRange('created_at', 'Created');
        $this->assertSame('created_at', $filter->originalColumn());
        $this->assertSame('Created', $filter->getLabel());
    }

    #[DataProvider('dateRangeQuickVariableKeyProvider')]
    public function test_date_range_quick_presenter(string $key): void
    {
        $presenter = new DateRangeQuick;
        $variables = $presenter->defaultVariables();

        $this->assertContains($key, array_keys($variables));
        $this->assertTrue($variables['showDateInputs']);
    }

    public function test_date_range_quick_custom_ranges(): void
    {
        $customRanges = [
            'Custom Range' => ['2024-01-01', '2024-12-31'],
        ];
        $presenter = new DateRangeQuick($customRanges);
        $variables = $presenter->defaultVariables();

        $this->assertSame($customRanges, $variables['ranges']);
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

        $this->assertSame('YYYY-MM-DD HH:mm:ss', $variables['dateOptions']['format']);
    }

    public function test_date_range_condition_start_only(): void
    {
        $filter = new DateRange('created_at', 'Created');

        $this->attachParentFilter($filter);

        $condition = $filter->condition(['created_at' => ['start' => '2024-01-01', 'end' => '']]);

        $this->assertConditionHasKey($condition, 'where');
    }

    public function test_date_range_condition_end_only(): void
    {
        $filter = new DateRange('created_at', 'Created');

        $this->attachParentFilter($filter);

        $condition = $filter->condition(['created_at' => ['start' => '', 'end' => '2024-12-31']]);

        $this->assertConditionHasKey($condition, 'where');
    }

    public function test_date_range_condition_both(): void
    {
        $filter = new DateRange('created_at', 'Created');

        $this->attachParentFilter($filter);

        $condition = $filter->condition(['created_at' => ['start' => '2024-01-01', 'end' => '2024-12-31']]);

        $this->assertConditionHasKey($condition, 'whereBetween');
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
        $this->assertSame('is_active', $filter->originalColumn());
        $this->assertSame('Active', $filter->getLabel());
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

        $this->assertSame('On', $variables['onText']);
        $this->assertSame('Off', $variables['offText']);
        $this->assertSame(1, $variables['onValue']);
        $this->assertSame(0, $variables['offValue']);
        $this->assertSame('small', $variables['size']);
    }

    public function test_toggle_presenter_values(): void
    {
        $presenter = new TogglePresenter;
        $presenter->values('yes', 'no');
        $variables = $presenter->defaultVariables();

        $this->assertSame('yes', $variables['onValue']);
        $this->assertSame('no', $variables['offValue']);
    }

    public function test_toggle_presenter_size(): void
    {
        $presenter = new TogglePresenter;
        $presenter->size('large');
        $variables = $presenter->defaultVariables();

        $this->assertSame('large', $variables['size']);
    }

    public function test_toggle_condition_with_value(): void
    {
        $filter = new Toggle('is_active', 'Active');

        $this->attachParentFilter($filter);

        $condition = $filter->condition(['is_active' => '1']);

        $this->assertConditionHasKey($condition, 'where');
    }

    public function test_toggle_condition_without_value(): void
    {
        $filter = new Toggle('is_active', 'Active');

        $condition = $filter->condition(['is_active' => '']);

        $this->assertNull($condition);
    }

    public static function dateRangeQuickVariableKeyProvider(): array
    {
        return [
            ['ranges'],
            ['dateOptions'],
            ['showDateInputs'],
        ];
    }

    private function assertConditionHasKey(mixed $condition, string $key): void
    {
        $this->assertIsArray($condition);
        $this->assertContains($key, array_keys($condition));
    }
}
