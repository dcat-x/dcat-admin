<?php

namespace Dcat\Admin\Tests\Unit\Grid\Filter;

use Dcat\Admin\Grid\Filter\DateRange;
use Dcat\Admin\Tests\TestCase;

class DateRangeFilterTest extends TestCase
{
    protected function makeFilter(string $column, string $label = ''): DateRange
    {
        $filter = new DateRange($column, $label);

        $grid = $this->createMock(\Dcat\Admin\Grid::class);
        $grid->method('makeName')->willReturnCallback(fn ($name) => $name);

        $parentFilter = $this->createMock(\Dcat\Admin\Grid\Filter::class);
        $parentFilter->method('grid')->willReturn($grid);

        $filter->setParent($parentFilter);

        return $filter;
    }

    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    public function test_default_width_is_twelve(): void
    {
        $filter = $this->makeFilter('created_at');

        $this->assertSame(12, $this->getProtectedProperty($filter, 'width'));
    }

    public function test_default_timestamp_is_false(): void
    {
        $filter = $this->makeFilter('created_at');

        $this->assertFalse($this->getProtectedProperty($filter, 'timestamp'));
    }

    public function test_to_timestamp_is_fluent(): void
    {
        $filter = $this->makeFilter('created_at');

        $result = $filter->toTimestamp();

        $this->assertSame($filter, $result);
    }

    public function test_to_timestamp_sets_timestamp_to_true(): void
    {
        $filter = $this->makeFilter('created_at');

        $filter->toTimestamp();

        $this->assertTrue($this->getProtectedProperty($filter, 'timestamp'));
    }

    public function test_condition_returns_null_when_column_missing(): void
    {
        $filter = $this->makeFilter('created_at');

        $condition = $filter->condition(['other' => ['start' => '2024-01-01', 'end' => '2024-12-31']]);

        $this->assertNull($condition);
    }

    public function test_condition_returns_null_when_both_values_empty(): void
    {
        $filter = $this->makeFilter('created_at');

        $condition = $filter->condition(['created_at' => ['start' => '', 'end' => '']]);

        $this->assertNull($condition);
    }

    public function test_condition_with_start_only_returns_gte(): void
    {
        $filter = $this->makeFilter('created_at');

        $condition = $filter->condition(['created_at' => ['start' => '2024-01-01', 'end' => '']]);

        $this->assertConditionHasKey($condition, 'where');
        $this->assertSame(['created_at', '>=', '2024-01-01'], $condition['where']);
    }

    public function test_condition_with_end_only_returns_lte(): void
    {
        $filter = $this->makeFilter('created_at');

        $condition = $filter->condition(['created_at' => ['start' => '', 'end' => '2024-12-31']]);

        $this->assertConditionHasKey($condition, 'where');
        $this->assertSame(['created_at', '<=', '2024-12-31'], $condition['where']);
    }

    public function test_condition_with_both_start_and_end_returns_where_between(): void
    {
        $filter = $this->makeFilter('created_at');

        $condition = $filter->condition(['created_at' => ['start' => '2024-01-01', 'end' => '2024-12-31']]);

        $this->assertConditionHasKey($condition, 'whereBetween');
        $this->assertSame(['created_at', ['2024-01-01', '2024-12-31']], $condition['whereBetween']);
    }

    public function test_condition_converts_to_timestamp_when_enabled(): void
    {
        $filter = $this->makeFilter('created_at');
        $filter->toTimestamp();

        $condition = $filter->condition(['created_at' => ['start' => '2024-01-01', 'end' => '2024-12-31']]);

        $this->assertConditionHasKey($condition, 'whereBetween');
        $values = $condition['whereBetween'][1];
        $this->assertSame(strtotime('2024-01-01'), $values[0]);
        $this->assertSame(strtotime('2024-12-31'), $values[1]);
    }

    public function test_condition_sets_value_on_filter(): void
    {
        $filter = $this->makeFilter('created_at');

        $filter->condition(['created_at' => ['start' => '2024-01-01', 'end' => '2024-12-31']]);

        $this->assertSame(['start' => '2024-01-01', 'end' => '2024-12-31'], $filter->getValue());
    }

    private function assertConditionHasKey(mixed $condition, string $key): void
    {
        $this->assertIsArray($condition);
        $this->assertContains($key, array_keys($condition));
    }
}
