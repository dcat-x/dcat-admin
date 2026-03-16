<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Filter;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Filter;
use Dcat\Admin\Grid\Filter\Equal;
use Dcat\Admin\Tests\TestCase;

class EqualTest extends TestCase
{
    protected function makeFilter(string $column, string $label = ''): Equal
    {
        $filter = new Equal($column, $label);

        $grid = $this->createMock(Grid::class);
        $grid->method('makeName')->willReturnCallback(fn ($name) => $name);

        $parentFilter = $this->createMock(Filter::class);
        $parentFilter->method('grid')->willReturn($grid);

        $filter->setParent($parentFilter);

        return $filter;
    }

    public function test_constructor_sets_column_and_label(): void
    {
        $filter = new Equal('status', 'Status');

        $this->assertSame('status', $filter->originalColumn());
        $this->assertSame('Status', $filter->getLabel());
    }

    public function test_constructor_auto_generates_label_from_column(): void
    {
        $filter = new Equal('user_name');

        $this->assertSame('user_name', $filter->originalColumn());
    }

    public function test_condition_returns_where_with_value(): void
    {
        $filter = $this->makeFilter('status', 'Status');

        $condition = $filter->condition(['status' => 'active']);

        $this->assertConditionHasWhere($condition);
        $this->assertSame(['status', 'active'], $condition['where']);
    }

    public function test_condition_returns_null_when_value_is_null(): void
    {
        $filter = $this->makeFilter('status', 'Status');

        $condition = $filter->condition(['other' => 'value']);

        $this->assertNull($condition);
    }

    public function test_condition_with_numeric_value(): void
    {
        $filter = $this->makeFilter('type', 'Type');

        $condition = $filter->condition(['type' => '1']);

        $this->assertConditionHasWhere($condition);
        $this->assertSame(['type', '1'], $condition['where']);
    }

    public function test_condition_with_zero_value(): void
    {
        $filter = $this->makeFilter('status', 'Status');

        $condition = $filter->condition(['status' => '0']);

        $this->assertConditionHasWhere($condition);
        $this->assertSame(['status', '0'], $condition['where']);
    }

    public function test_condition_with_empty_string_value(): void
    {
        $filter = $this->makeFilter('status', 'Status');

        $condition = $filter->condition(['status' => '']);

        $this->assertConditionHasWhere($condition);
        $this->assertSame(['status', ''], $condition['where']);
    }

    public function test_condition_sets_value_on_filter(): void
    {
        $filter = $this->makeFilter('status', 'Status');

        $filter->condition(['status' => 'active']);

        $this->assertSame('active', $filter->getValue());
    }

    public function test_ignore_returns_null_condition(): void
    {
        $filter = $this->makeFilter('status', 'Status');
        $filter->ignore();

        $condition = $filter->condition(['status' => 'active']);

        $this->assertNull($condition);
    }

    private function assertConditionHasWhere(mixed $condition): void
    {
        $this->assertIsArray($condition);
        $this->assertContains('where', array_keys($condition));
    }
}
