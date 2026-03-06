<?php

namespace Dcat\Admin\Tests\Unit\Grid\Filter;

use Dcat\Admin\Grid\Filter\NotEqual;
use Dcat\Admin\Tests\TestCase;

class NotEqualTest extends TestCase
{
    protected function makeFilter(string $column, string $label = ''): NotEqual
    {
        $filter = new NotEqual($column, $label);

        $grid = $this->createMock(\Dcat\Admin\Grid::class);
        $grid->method('makeName')->willReturnCallback(fn ($name) => $name);

        $parentFilter = $this->createMock(\Dcat\Admin\Grid\Filter::class);
        $parentFilter->method('grid')->willReturn($grid);

        $filter->setParent($parentFilter);

        return $filter;
    }

    public function test_constructor_sets_column_and_label(): void
    {
        $filter = new NotEqual('status', 'Exclude Status');

        $this->assertSame('status', $filter->originalColumn());
        $this->assertSame('Exclude Status', $filter->getLabel());
    }

    public function test_condition_returns_where_with_not_equal_operator(): void
    {
        $filter = $this->makeFilter('status', 'Status');

        $condition = $filter->condition(['status' => 'deleted']);

        $this->assertConditionHasWhere($condition);
        $this->assertSame(['status', '!=', 'deleted'], $condition['where']);
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

        $condition = $filter->condition(['type' => '0']);

        $this->assertConditionHasWhere($condition);
        $this->assertSame(['type', '!=', '0'], $condition['where']);
    }

    public function test_condition_sets_value_on_filter(): void
    {
        $filter = $this->makeFilter('status', 'Status');

        $filter->condition(['status' => 'banned']);

        $this->assertSame('banned', $filter->getValue());
    }

    public function test_condition_with_empty_string(): void
    {
        $filter = $this->makeFilter('status', 'Status');

        $condition = $filter->condition(['status' => '']);

        $this->assertConditionHasWhere($condition);
        $this->assertSame(['status', '!=', ''], $condition['where']);
    }

    public function test_ignore_returns_null_condition(): void
    {
        $filter = $this->makeFilter('status', 'Status');
        $filter->ignore();

        $condition = $filter->condition(['status' => 'deleted']);

        $this->assertNull($condition);
    }

    private function assertConditionHasWhere(mixed $condition): void
    {
        $this->assertIsArray($condition);
        $this->assertContains('where', array_keys($condition));
    }
}
