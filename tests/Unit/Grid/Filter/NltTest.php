<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Filter;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Filter;
use Dcat\Admin\Grid\Filter\Nlt;
use Dcat\Admin\Tests\TestCase;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

#[AllowMockObjectsWithoutExpectations]
class NltTest extends TestCase
{
    protected function makeFilter(string $column, string $label = ''): Nlt
    {
        $filter = new Nlt($column, $label);

        $grid = $this->createMock(Grid::class);
        $grid->method('makeName')->willReturnCallback(fn ($name) => $name);

        $parentFilter = $this->createMock(Filter::class);
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

    public function test_view_property_is_filter_lt(): void
    {
        $filter = $this->makeFilter('price');

        $this->assertSame('admin::filter.lt', $this->getProtectedProperty($filter, 'view'));
    }

    public function test_constructor_sets_column_and_label(): void
    {
        $filter = new Nlt('price', 'Min Price');

        $this->assertSame('price', $filter->originalColumn());
        $this->assertSame('Min Price', $filter->getLabel());
    }

    public function test_condition_returns_where_with_gte_operator(): void
    {
        $filter = $this->makeFilter('price', 'Min Price');

        $condition = $filter->condition(['price' => '100']);

        $this->assertConditionHasWhere($condition);
        $this->assertSame(['price', '>=', '100'], $condition['where']);
    }

    public function test_condition_returns_null_when_value_is_null(): void
    {
        $filter = $this->makeFilter('price');

        $condition = $filter->condition(['other' => 'value']);

        $this->assertNull($condition);
    }

    public function test_condition_sets_value_on_filter(): void
    {
        $filter = $this->makeFilter('price');

        $filter->condition(['price' => '50']);

        $this->assertSame('50', $filter->getValue());
    }

    public function test_condition_with_zero_value(): void
    {
        $filter = $this->makeFilter('score');

        $condition = $filter->condition(['score' => '0']);

        $this->assertConditionHasWhere($condition);
        $this->assertSame(['score', '>=', '0'], $condition['where']);
    }

    public function test_ignore_returns_null_condition(): void
    {
        $filter = $this->makeFilter('price');
        $filter->ignore();

        $condition = $filter->condition(['price' => '100']);

        $this->assertNull($condition);
    }

    private function assertConditionHasWhere(mixed $condition): void
    {
        $this->assertIsArray($condition);
        $this->assertContains('where', array_keys($condition));
    }
}
