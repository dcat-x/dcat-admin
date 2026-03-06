<?php

namespace Dcat\Admin\Tests\Unit\Grid\Filter;

use Dcat\Admin\Grid\Filter\In;
use Dcat\Admin\Tests\TestCase;

class InTest extends TestCase
{
    protected function makeFilter(string $column, string $label = ''): In
    {
        $filter = new In($column, $label);

        $grid = $this->createMock(\Dcat\Admin\Grid::class);
        $grid->method('makeName')->willReturnCallback(fn ($name) => $name);

        $parentFilter = $this->createMock(\Dcat\Admin\Grid\Filter::class);
        $parentFilter->method('grid')->willReturn($grid);

        $filter->setParent($parentFilter);

        return $filter;
    }

    public function test_constructor_sets_column_and_label(): void
    {
        $filter = new In('status', 'Status');

        $this->assertSame('status', $filter->originalColumn());
        $this->assertSame('Status', $filter->getLabel());
    }

    public function test_condition_returns_where_in_with_array_value(): void
    {
        $filter = $this->makeFilter('status', 'Status');

        $condition = $filter->condition(['status' => ['active', 'pending']]);

        $this->assertConditionHasWhereIn($condition);
        $this->assertSame(['status', ['active', 'pending']], $condition['whereIn']);
    }

    public function test_condition_splits_comma_separated_string(): void
    {
        $filter = $this->makeFilter('status', 'Status');

        $condition = $filter->condition(['status' => 'active,pending,closed']);

        $this->assertConditionHasWhereIn($condition);
        $this->assertSame(['status', ['active', 'pending', 'closed']], $condition['whereIn']);
    }

    public function test_condition_returns_null_when_value_is_null(): void
    {
        $filter = $this->makeFilter('status', 'Status');

        $condition = $filter->condition(['other' => 'value']);

        $this->assertNull($condition);
    }

    public function test_condition_with_single_value_as_string(): void
    {
        $filter = $this->makeFilter('category', 'Category');

        $condition = $filter->condition(['category' => 'news']);

        $this->assertConditionHasWhereIn($condition);
        $this->assertSame(['category', ['news']], $condition['whereIn']);
    }

    public function test_condition_sets_array_value_on_filter(): void
    {
        $filter = $this->makeFilter('status', 'Status');

        $filter->condition(['status' => ['a', 'b']]);

        $this->assertSame(['a', 'b'], $filter->getValue());
    }

    public function test_condition_converts_string_to_array_value(): void
    {
        $filter = $this->makeFilter('status', 'Status');

        $filter->condition(['status' => 'x,y']);

        $this->assertSame(['x', 'y'], $filter->getValue());
    }

    public function test_ignore_returns_null_condition(): void
    {
        $filter = $this->makeFilter('status', 'Status');
        $filter->ignore();

        $condition = $filter->condition(['status' => ['active']]);

        $this->assertNull($condition);
    }

    private function assertConditionHasWhereIn(mixed $condition): void
    {
        $this->assertIsArray($condition);
        $this->assertContains('whereIn', array_keys($condition));
    }
}
