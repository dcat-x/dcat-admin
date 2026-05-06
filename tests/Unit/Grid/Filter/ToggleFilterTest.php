<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Filter;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Filter;
use Dcat\Admin\Grid\Filter\Toggle;
use Dcat\Admin\Tests\TestCase;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

#[AllowMockObjectsWithoutExpectations]
class ToggleFilterTest extends TestCase
{
    protected function makeFilter(string $column, string $label = ''): Toggle
    {
        $filter = new Toggle($column, $label);

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

    public function test_default_on_value_is_one(): void
    {
        $filter = $this->makeFilter('active');

        $this->assertSame(1, $this->getProtectedProperty($filter, 'onValue'));
    }

    public function test_default_off_value_is_zero(): void
    {
        $filter = $this->makeFilter('active');

        $this->assertSame(0, $this->getProtectedProperty($filter, 'offValue'));
    }

    public function test_values_setter_is_fluent(): void
    {
        $filter = $this->makeFilter('active');

        $result = $filter->values('yes', 'no');

        $this->assertSame($filter, $result);
    }

    public function test_values_setter_updates_on_and_off_values(): void
    {
        $filter = $this->makeFilter('active');

        $filter->values('yes', 'no');

        $this->assertSame('yes', $this->getProtectedProperty($filter, 'onValue'));
        $this->assertSame('no', $this->getProtectedProperty($filter, 'offValue'));
    }

    public function test_condition_returns_null_when_value_is_null(): void
    {
        $filter = $this->makeFilter('active');

        $condition = $filter->condition(['other' => '1']);

        $this->assertNull($condition);
    }

    public function test_condition_returns_null_when_value_is_empty_string(): void
    {
        $filter = $this->makeFilter('active');

        $condition = $filter->condition(['active' => '']);

        $this->assertNull($condition);
    }

    public function test_condition_returns_build_condition_for_valid_value(): void
    {
        $filter = $this->makeFilter('active');

        $condition = $filter->condition(['active' => '1']);

        $this->assertConditionHasWhere($condition);
        $this->assertSame(['active', '1'], $condition['where']);
    }

    public function test_condition_sets_value_on_filter(): void
    {
        $filter = $this->makeFilter('active');

        $filter->condition(['active' => '1']);

        $this->assertSame('1', $filter->getValue());
    }

    private function assertConditionHasWhere(mixed $condition): void
    {
        $this->assertIsArray($condition);
        $this->assertContains('where', array_keys($condition));
    }
}
