<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Filter;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Filter;
use Dcat\Admin\Grid\Filter\Date;
use Dcat\Admin\Grid\Filter\Month;
use Dcat\Admin\Tests\TestCase;

class MonthFilterTest extends TestCase
{
    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    protected function makeFilter(string $column, string $label = ''): Month
    {
        $filter = new Month($column, $label);

        $grid = $this->createMock(Grid::class);
        $grid->method('makeName')->willReturnCallback(fn ($name) => $name);

        $parentFilter = $this->createMock(Filter::class);
        $parentFilter->method('grid')->willReturn($grid);

        $filter->setParent($parentFilter);

        return $filter;
    }

    public function test_extends_date(): void
    {
        $filter = $this->makeFilter('created_at');

        $this->assertInstanceOf(Date::class, $filter);
    }

    public function test_query_property_is_where_month(): void
    {
        $filter = $this->makeFilter('created_at');

        $this->assertSame('whereMonth', $this->getProtectedProperty($filter, 'query'));
    }

    public function test_field_name_property_is_month(): void
    {
        $filter = $this->makeFilter('created_at');

        $this->assertSame('month', $this->getProtectedProperty($filter, 'fieldName'));
    }

    public function test_constructor_sets_column(): void
    {
        $filter = $this->makeFilter('published_at', 'Published At');

        $this->assertSame('published_at', $filter->originalColumn());
    }

    public function test_constructor_sets_label(): void
    {
        $filter = $this->makeFilter('published_at', 'Published At');

        $this->assertSame('Published At', $filter->getLabel());
    }
}
