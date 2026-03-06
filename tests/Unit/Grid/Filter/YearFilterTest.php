<?php

namespace Dcat\Admin\Tests\Unit\Grid\Filter;

use Dcat\Admin\Grid\Filter\Date;
use Dcat\Admin\Grid\Filter\Year;
use Dcat\Admin\Tests\TestCase;

class YearFilterTest extends TestCase
{
    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    protected function makeFilter(string $column, string $label = ''): Year
    {
        $filter = new Year($column, $label);

        $grid = $this->createMock(\Dcat\Admin\Grid::class);
        $grid->method('makeName')->willReturnCallback(fn ($name) => $name);

        $parentFilter = $this->createMock(\Dcat\Admin\Grid\Filter::class);
        $parentFilter->method('grid')->willReturn($grid);

        $filter->setParent($parentFilter);

        return $filter;
    }

    public function test_extends_date(): void
    {
        $filter = $this->makeFilter('created_at');

        $this->assertInstanceOf(Date::class, $filter);
    }

    public function test_query_property_is_where_year(): void
    {
        $filter = $this->makeFilter('created_at');

        $this->assertSame('whereYear', $this->getProtectedProperty($filter, 'query'));
    }

    public function test_field_name_property_is_year(): void
    {
        $filter = $this->makeFilter('created_at');

        $this->assertSame('year', $this->getProtectedProperty($filter, 'fieldName'));
    }

    public function test_constructor_sets_column(): void
    {
        $filter = $this->makeFilter('updated_at', 'Updated At');

        $this->assertSame('updated_at', $filter->originalColumn());
    }

    public function test_constructor_sets_label(): void
    {
        $filter = $this->makeFilter('updated_at', 'Updated At');

        $this->assertSame('Updated At', $filter->getLabel());
    }
}
