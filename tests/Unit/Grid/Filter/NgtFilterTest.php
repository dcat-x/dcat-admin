<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Filter;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Filter;
use Dcat\Admin\Grid\Filter\AbstractFilter;
use Dcat\Admin\Grid\Filter\Ngt;
use Dcat\Admin\Tests\TestCase;

class NgtFilterTest extends TestCase
{
    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    protected function makeFilter(string $column, string $label = ''): Ngt
    {
        $filter = new Ngt($column, $label);

        $grid = $this->createMock(Grid::class);
        $grid->method('makeName')->willReturnCallback(fn ($name) => $name);

        $parentFilter = $this->createMock(Filter::class);
        $parentFilter->method('grid')->willReturn($grid);

        $filter->setParent($parentFilter);

        return $filter;
    }

    public function test_extends_abstract_filter(): void
    {
        $filter = $this->makeFilter('price');

        $this->assertInstanceOf(AbstractFilter::class, $filter);
    }

    public function test_view_property_is_filter_gt(): void
    {
        $filter = $this->makeFilter('price');

        $this->assertSame('admin::filter.gt', $this->getProtectedProperty($filter, 'view'));
    }

    public function test_condition_returns_less_than_or_equal_where(): void
    {
        $filter = $this->makeFilter('price', 'Price');

        $condition = $filter->condition(['price' => '100']);

        $this->assertConditionHasWhere($condition);
        $this->assertSame(['price', '<=', '100'], $condition['where']);
    }

    public function test_condition_returns_null_when_value_is_null(): void
    {
        $filter = $this->makeFilter('price', 'Price');

        $condition = $filter->condition(['other' => 'value']);

        $this->assertNull($condition);
    }

    public function test_condition_sets_value_property(): void
    {
        $filter = $this->makeFilter('price', 'Price');

        $filter->condition(['price' => '50']);

        $this->assertSame('50', $filter->getValue());
    }

    public function test_condition_with_numeric_value(): void
    {
        $filter = $this->makeFilter('amount', 'Amount');

        $condition = $filter->condition(['amount' => '999']);

        $this->assertConditionHasWhere($condition);
        $this->assertSame('<=', $condition['where'][1]);
        $this->assertSame('999', $condition['where'][2]);
    }

    private function assertConditionHasWhere(mixed $condition): void
    {
        $this->assertIsArray($condition);
        $this->assertContains('where', array_keys($condition));
    }
}
