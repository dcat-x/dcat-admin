<?php

namespace Dcat\Admin\Tests\Unit\Grid\Filter;

use Dcat\Admin\Grid\Filter\AbstractFilter;
use Dcat\Admin\Grid\Filter\Date;
use Dcat\Admin\Tests\TestCase;

class DateFilterTest extends TestCase
{
    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    protected function makeFilter(string $column, string $label = ''): Date
    {
        $filter = new Date($column, $label);

        $grid = $this->createMock(\Dcat\Admin\Grid::class);
        $grid->method('makeName')->willReturnCallback(fn ($name) => $name);

        $parentFilter = $this->createMock(\Dcat\Admin\Grid\Filter::class);
        $parentFilter->method('grid')->willReturn($grid);

        $filter->setParent($parentFilter);

        return $filter;
    }

    public function test_query_property_is_where_date(): void
    {
        $filter = $this->makeFilter('created_at');

        $this->assertSame('whereDate', $this->getProtectedProperty($filter, 'query'));
    }

    public function test_field_name_property_is_date(): void
    {
        $filter = $this->makeFilter('created_at');

        $this->assertSame('date', $this->getProtectedProperty($filter, 'fieldName'));
    }

    public function test_extends_abstract_filter(): void
    {
        $filter = $this->makeFilter('created_at');

        $this->assertInstanceOf(AbstractFilter::class, $filter);
    }

    public function test_constructor_sets_column(): void
    {
        $filter = $this->makeFilter('created_at', 'Created At');

        $this->assertSame('created_at', $filter->originalColumn());
    }

    public function test_constructor_sets_label(): void
    {
        $filter = $this->makeFilter('created_at', 'Created At');

        $this->assertSame('Created At', $filter->getLabel());
    }

    public function test_constructor_without_label(): void
    {
        $filter = $this->makeFilter('created_at');

        $this->assertSame('created_at', $filter->originalColumn());
    }
}
