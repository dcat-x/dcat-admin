<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Filter;

use Dcat\Admin\Grid\Filter\WhereNotNull;
use Dcat\Admin\Tests\TestCase;

class WhereNotNullTest extends TestCase
{
    protected function makeFilter(string $column, string $label = ''): WhereNotNull
    {
        $filter = new WhereNotNull($column, $label);

        $grid = $this->createMock(\Dcat\Admin\Grid::class);
        $grid->method('makeName')->willReturnCallback(fn ($name) => $name);

        $parentFilter = $this->createMock(\Dcat\Admin\Grid\Filter::class);
        $parentFilter->method('grid')->willReturn($grid);

        $filter->setParent($parentFilter);

        return $filter;
    }

    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    public function test_query_property_is_where_not_null(): void
    {
        $filter = $this->makeFilter('name');

        $this->assertSame('whereNotNull', $this->getProtectedProperty($filter, 'query'));
    }

    public function test_condition_returns_null_when_value_is_null(): void
    {
        $filter = $this->makeFilter('name');

        $condition = $filter->condition(['other' => '1']);

        $this->assertNull($condition);
    }

    public function test_condition_returns_null_when_value_is_empty_string(): void
    {
        $filter = $this->makeFilter('name');

        $condition = $filter->condition(['name' => '']);

        $this->assertNull($condition);
    }

    public function test_condition_returns_where_not_null_for_truthy_value(): void
    {
        $filter = $this->makeFilter('name');

        $condition = $filter->condition(['name' => '1']);

        $this->assertConditionHasWhereNotNull($condition);
        $this->assertSame(['name'], $condition['whereNotNull']);
    }

    public function test_condition_returns_null_for_falsy_value(): void
    {
        $filter = $this->makeFilter('name');

        $condition = $filter->condition(['name' => '0']);

        $this->assertNull($condition);
    }

    public function test_condition_sets_value_on_filter(): void
    {
        $filter = $this->makeFilter('name');

        $filter->condition(['name' => '1']);

        $this->assertSame('1', $filter->getValue());
    }

    public function test_constructor_sets_column_and_label(): void
    {
        $filter = new WhereNotNull('name', 'Has Name');

        $this->assertSame('name', $filter->originalColumn());
        $this->assertSame('Has Name', $filter->getLabel());
    }

    private function assertConditionHasWhereNotNull(mixed $condition): void
    {
        $this->assertIsArray($condition);
        $this->assertContains('whereNotNull', array_keys($condition));
    }
}
