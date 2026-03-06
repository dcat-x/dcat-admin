<?php

namespace Dcat\Admin\Tests\Unit\Grid\Filter;

use Dcat\Admin\Grid\Filter\AbstractFilter;
use Dcat\Admin\Grid\Filter\Ilike;
use Dcat\Admin\Tests\TestCase;

class IlikeTest extends TestCase
{
    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    protected function makeFilter(string $column, string $label = ''): Ilike
    {
        $filter = new Ilike($column, $label);

        $grid = $this->createMock(\Dcat\Admin\Grid::class);
        $grid->method('makeName')->willReturnCallback(fn ($name) => $name);

        $parentFilter = $this->createMock(\Dcat\Admin\Grid\Filter::class);
        $parentFilter->method('grid')->willReturn($grid);

        $filter->setParent($parentFilter);

        return $filter;
    }

    public function test_extends_abstract_filter(): void
    {
        $filter = $this->makeFilter('name');

        $this->assertInstanceOf(AbstractFilter::class, $filter);
    }

    public function test_condition_returns_ilike_where_with_wildcards(): void
    {
        $filter = $this->makeFilter('name', 'Name');

        $condition = $filter->condition(['name' => 'john']);

        $this->assertConditionHasWhere($condition);
        $this->assertSame(['name', 'ilike', '%john%'], $condition['where']);
    }

    public function test_condition_returns_null_when_value_is_null(): void
    {
        $filter = $this->makeFilter('name', 'Name');

        $condition = $filter->condition(['other' => 'value']);

        $this->assertNull($condition);
    }

    public function test_condition_sets_value_property(): void
    {
        $filter = $this->makeFilter('name', 'Name');

        $filter->condition(['name' => 'search_term']);

        $this->assertSame('search_term', $filter->getValue());
    }

    public function test_condition_wraps_value_with_wildcards(): void
    {
        $filter = $this->makeFilter('title', 'Title');

        $condition = $filter->condition(['title' => 'test']);

        $this->assertSame('%test%', $condition['where'][2]);
    }

    public function test_condition_with_empty_string_returns_wildcards(): void
    {
        $filter = $this->makeFilter('name', 'Name');

        $condition = $filter->condition(['name' => '']);

        $this->assertConditionHasWhere($condition);
        $this->assertSame(['name', 'ilike', '%%'], $condition['where']);
    }

    private function assertConditionHasWhere(mixed $condition): void
    {
        $this->assertIsArray($condition);
        $this->assertContains('where', array_keys($condition));
    }
}
