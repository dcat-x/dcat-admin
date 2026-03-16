<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Filter;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Filter;
use Dcat\Admin\Grid\Filter\Between;
use Dcat\Admin\Tests\TestCase;

class BetweenTest extends TestCase
{
    protected function makeFilter(string $column, string $label = ''): Between
    {
        $filter = new Between($column, $label);

        $grid = $this->createMock(Grid::class);
        $grid->method('makeName')->willReturnCallback(fn ($name) => $name);

        $parentFilter = $this->createMock(Filter::class);
        $parentFilter->method('grid')->willReturn($grid);

        $filter->setParent($parentFilter);

        return $filter;
    }

    public function test_constructor_sets_column_and_label(): void
    {
        $filter = new Between('price', 'Price Range');

        $this->assertSame('price', $filter->originalColumn());
        $this->assertSame('Price Range', $filter->getLabel());
    }

    public function test_condition_with_both_start_and_end(): void
    {
        $filter = $this->makeFilter('price', 'Price');

        $condition = $filter->condition(['price' => ['start' => '100', 'end' => '500']]);

        $this->assertConditionHasKey($condition, 'whereBetween');
        $this->assertSame(['price', ['100', '500']], $condition['whereBetween']);
    }

    public function test_condition_with_start_only(): void
    {
        $filter = $this->makeFilter('price', 'Price');

        $condition = $filter->condition(['price' => ['start' => '100', 'end' => '']]);

        $this->assertConditionHasKey($condition, 'where');
        $this->assertSame(['price', '>=', '100'], $condition['where']);
    }

    public function test_condition_with_end_only(): void
    {
        $filter = $this->makeFilter('price', 'Price');

        $condition = $filter->condition(['price' => ['start' => '', 'end' => '500']]);

        $this->assertConditionHasKey($condition, 'where');
        $this->assertSame(['price', '<=', '500'], $condition['where']);
    }

    public function test_condition_returns_null_when_both_empty(): void
    {
        $filter = $this->makeFilter('price', 'Price');

        $condition = $filter->condition(['price' => ['start' => '', 'end' => '']]);

        $this->assertNull($condition);
    }

    public function test_condition_returns_null_when_column_missing(): void
    {
        $filter = $this->makeFilter('price', 'Price');

        $condition = $filter->condition(['other' => 'value']);

        $this->assertNull($condition);
    }

    public function test_to_timestamp_converts_values(): void
    {
        $filter = $this->makeFilter('created_at', 'Created');
        $filter->toTimestamp();

        $condition = $filter->condition(['created_at' => ['start' => '2024-01-01', 'end' => '2024-12-31']]);

        $this->assertConditionHasKey($condition, 'whereBetween');

        $values = $condition['whereBetween'][1];
        $this->assertSame(strtotime('2024-01-01'), $values[0]);
        $this->assertSame(strtotime('2024-12-31'), $values[1]);
    }

    public function test_to_timestamp_with_start_only(): void
    {
        $filter = $this->makeFilter('created_at', 'Created');
        $filter->toTimestamp();

        $condition = $filter->condition(['created_at' => ['start' => '2024-06-15', 'end' => '']]);

        $this->assertConditionHasKey($condition, 'where');
        $this->assertSame(strtotime('2024-06-15'), $condition['where'][2]);
    }

    public function test_to_timestamp_returns_self(): void
    {
        $filter = $this->makeFilter('created_at', 'Created');

        $result = $filter->toTimestamp();

        $this->assertInstanceOf(Between::class, $result);
    }

    public function test_format_id_returns_start_end_array(): void
    {
        $filter = $this->makeFilter('price', 'Price');

        $id = $filter->formatId('price');

        $this->assertIsArray($id);
        $this->assertArrayContainsKeys(['start', 'end'], $id);
        $this->assertSame('filter-column-price-start', $id['start']);
        $this->assertSame('filter-column-price-end', $id['end']);
    }

    public function test_ignore_returns_null_condition(): void
    {
        $filter = $this->makeFilter('price', 'Price');
        $filter->ignore();

        $condition = $filter->condition(['price' => ['start' => '100', 'end' => '500']]);

        $this->assertNull($condition);
    }

    public function test_condition_sets_value_on_filter(): void
    {
        $filter = $this->makeFilter('price', 'Price');

        $filter->condition(['price' => ['start' => '10', 'end' => '20']]);

        $this->assertSame(['start' => '10', 'end' => '20'], $filter->getValue());
    }

    private function assertConditionHasKey(mixed $condition, string $key): void
    {
        $this->assertIsArray($condition);
        $this->assertContains($key, array_keys($condition));
    }

    private function assertArrayContainsKeys(array $expectedKeys, array $actual): void
    {
        foreach ($expectedKeys as $key) {
            $this->assertContains($key, array_keys($actual));
        }
    }
}
