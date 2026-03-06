<?php

namespace Dcat\Admin\Tests\Unit\Grid\Filter;

use Dcat\Admin\Grid\Filter\Lt;
use Dcat\Admin\Tests\TestCase;

class LtTest extends TestCase
{
    protected function makeFilter(string $column, string $label = ''): Lt
    {
        $filter = new Lt($column, $label);

        $grid = $this->createMock(\Dcat\Admin\Grid::class);
        $grid->method('makeName')->willReturnCallback(fn ($name) => $name);

        $parentFilter = $this->createMock(\Dcat\Admin\Grid\Filter::class);
        $parentFilter->method('grid')->willReturn($grid);

        $filter->setParent($parentFilter);

        return $filter;
    }

    public function test_constructor_sets_column_and_label(): void
    {
        $filter = new Lt('price', 'Max Price');

        $this->assertSame('price', $filter->originalColumn());
        $this->assertSame('Max Price', $filter->getLabel());
    }

    public function test_condition_returns_where_with_lt_operator(): void
    {
        $filter = $this->makeFilter('price', 'Max Price');

        $condition = $filter->condition(['price' => '500']);

        $this->assertConditionHasWhere($condition);
        $this->assertSame(['price', '<', '500'], $condition['where']);
    }

    public function test_condition_returns_null_when_value_is_null(): void
    {
        $filter = $this->makeFilter('price', 'Max Price');

        $condition = $filter->condition(['other' => 'value']);

        $this->assertNull($condition);
    }

    public function test_condition_with_zero_value(): void
    {
        $filter = $this->makeFilter('score', 'Max Score');

        $condition = $filter->condition(['score' => '0']);

        $this->assertConditionHasWhere($condition);
        $this->assertSame(['score', '<', '0'], $condition['where']);
    }

    public function test_condition_sets_value_on_filter(): void
    {
        $filter = $this->makeFilter('price', 'Max Price');

        $filter->condition(['price' => '200']);

        $this->assertSame('200', $filter->getValue());
    }

    public function test_ignore_returns_null_condition(): void
    {
        $filter = $this->makeFilter('price', 'Max Price');
        $filter->ignore();

        $condition = $filter->condition(['price' => '500']);

        $this->assertNull($condition);
    }

    private function assertConditionHasWhere(mixed $condition): void
    {
        $this->assertIsArray($condition);
        $this->assertContains('where', array_keys($condition));
    }
}
