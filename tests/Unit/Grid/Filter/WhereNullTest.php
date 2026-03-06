<?php

namespace Dcat\Admin\Tests\Unit\Grid\Filter;

use Dcat\Admin\Grid\Filter\WhereNull;
use Dcat\Admin\Tests\TestCase;

class WhereNullTest extends TestCase
{
    protected function makeFilter(string $column, string $label = ''): WhereNull
    {
        $filter = new WhereNull($column, $label);

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

    public function test_query_property_is_where_null(): void
    {
        $filter = $this->makeFilter('deleted_at');

        $this->assertSame('whereNull', $this->getProtectedProperty($filter, 'query'));
    }

    public function test_condition_returns_null_when_value_is_null(): void
    {
        $filter = $this->makeFilter('deleted_at');

        $condition = $filter->condition(['other' => '1']);

        $this->assertNull($condition);
    }

    public function test_condition_returns_null_when_value_is_empty_string(): void
    {
        $filter = $this->makeFilter('deleted_at');

        $condition = $filter->condition(['deleted_at' => '']);

        $this->assertNull($condition);
    }

    public function test_condition_returns_where_null_for_truthy_value(): void
    {
        $filter = $this->makeFilter('deleted_at');

        $condition = $filter->condition(['deleted_at' => '1']);

        $this->assertConditionHasWhereNull($condition);
        $this->assertSame(['deleted_at'], $condition['whereNull']);
    }

    public function test_condition_returns_null_for_falsy_value(): void
    {
        $filter = $this->makeFilter('deleted_at');

        $condition = $filter->condition(['deleted_at' => '0']);

        $this->assertNull($condition);
    }

    public function test_condition_sets_value_on_filter(): void
    {
        $filter = $this->makeFilter('deleted_at');

        $filter->condition(['deleted_at' => '1']);

        $this->assertSame('1', $filter->getValue());
    }

    public function test_constructor_sets_column_and_label(): void
    {
        $filter = new WhereNull('deleted_at', 'Is Deleted');

        $this->assertSame('deleted_at', $filter->originalColumn());
        $this->assertSame('Is Deleted', $filter->getLabel());
    }

    private function assertConditionHasWhereNull(mixed $condition): void
    {
        $this->assertIsArray($condition);
        $this->assertContains('whereNull', array_keys($condition));
    }
}
