<?php

namespace Dcat\Admin\Tests\Unit\Grid\Filter;

use Dcat\Admin\Grid\Filter\NotIn;
use Dcat\Admin\Tests\TestCase;

class NotInTest extends TestCase
{
    protected function makeFilter(string $column, string $label = ''): NotIn
    {
        $filter = new NotIn($column, $label);

        $grid = $this->createMock(\Dcat\Admin\Grid::class);
        $grid->method('makeName')->willReturnCallback(fn ($name) => $name);

        $parentFilter = $this->createMock(\Dcat\Admin\Grid\Filter::class);
        $parentFilter->method('grid')->willReturn($grid);

        $filter->setParent($parentFilter);

        return $filter;
    }

    public function test_constructor_sets_column_and_label(): void
    {
        $filter = new NotIn('status', 'Exclude Status');

        $this->assertEquals('status', $filter->originalColumn());
        $this->assertEquals('Exclude Status', $filter->getLabel());
    }

    public function test_condition_returns_where_not_in_with_array_value(): void
    {
        $filter = $this->makeFilter('status', 'Exclude Status');

        $condition = $filter->condition(['status' => ['deleted', 'banned']]);

        $this->assertIsArray($condition);
        $this->assertArrayHasKey('whereNotIn', $condition);
        $this->assertEquals(['status', ['deleted', 'banned']], $condition['whereNotIn']);
    }

    public function test_condition_splits_comma_separated_string(): void
    {
        $filter = $this->makeFilter('status', 'Exclude Status');

        $condition = $filter->condition(['status' => 'deleted,banned']);

        $this->assertIsArray($condition);
        $this->assertArrayHasKey('whereNotIn', $condition);
        $this->assertEquals(['status', ['deleted', 'banned']], $condition['whereNotIn']);
    }

    public function test_condition_returns_null_when_value_is_null(): void
    {
        $filter = $this->makeFilter('status', 'Exclude Status');

        $condition = $filter->condition(['other' => 'value']);

        $this->assertNull($condition);
    }

    public function test_condition_with_single_value_string(): void
    {
        $filter = $this->makeFilter('role', 'Exclude Role');

        $condition = $filter->condition(['role' => 'admin']);

        $this->assertIsArray($condition);
        $this->assertArrayHasKey('whereNotIn', $condition);
        $this->assertEquals(['role', ['admin']], $condition['whereNotIn']);
    }

    public function test_condition_sets_array_value_on_filter(): void
    {
        $filter = $this->makeFilter('status', 'Exclude Status');

        $filter->condition(['status' => ['x', 'y']]);

        $this->assertEquals(['x', 'y'], $filter->getValue());
    }

    public function test_ignore_returns_null_condition(): void
    {
        $filter = $this->makeFilter('status', 'Exclude Status');
        $filter->ignore();

        $condition = $filter->condition(['status' => ['deleted']]);

        $this->assertNull($condition);
    }
}
