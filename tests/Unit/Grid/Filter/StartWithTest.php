<?php

namespace Dcat\Admin\Tests\Unit\Grid\Filter;

use Dcat\Admin\Grid\Filter\StartWith;
use Dcat\Admin\Tests\TestCase;

class StartWithTest extends TestCase
{
    protected function makeFilter(string $column, string $label = ''): StartWith
    {
        $filter = new StartWith($column, $label);

        $grid = $this->createMock(\Dcat\Admin\Grid::class);
        $grid->method('makeName')->willReturnCallback(fn ($name) => $name);

        $parentFilter = $this->createMock(\Dcat\Admin\Grid\Filter::class);
        $parentFilter->method('grid')->willReturn($grid);

        $filter->setParent($parentFilter);

        return $filter;
    }

    public function test_constructor_sets_column_and_label(): void
    {
        $filter = new StartWith('name', 'Name');
        $this->assertSame('name', $filter->originalColumn());
        $this->assertSame('Name', $filter->getLabel());
    }

    public function test_condition_returns_like_with_trailing_wildcard(): void
    {
        $filter = $this->makeFilter('name');
        $condition = $filter->condition(['name' => 'foo']);

        $this->assertConditionHasWhere($condition);
        $this->assertSame('name', $condition['where'][0]);
        $this->assertSame('like', $condition['where'][1]);
        $this->assertSame('foo%', $condition['where'][2]);
    }

    public function test_condition_returns_null_when_value_is_null(): void
    {
        $filter = $this->makeFilter('name');
        $result = $filter->condition(['other' => 'bar']);

        $this->assertNull($result);
    }

    public function test_ilike_changes_type_to_ilike(): void
    {
        $filter = $this->makeFilter('name');
        $result = $filter->ilike();

        $this->assertInstanceOf(StartWith::class, $result);

        $condition = $filter->condition(['name' => 'foo']);

        $this->assertSame('ilike', $condition['where'][1]);
        $this->assertSame('foo%', $condition['where'][2]);
    }

    public function test_condition_sets_value(): void
    {
        $filter = $this->makeFilter('name');
        $filter->condition(['name' => 'test']);

        $this->assertSame('test', $filter->getValue());
    }

    public function test_condition_with_empty_string_value(): void
    {
        $filter = $this->makeFilter('name');
        $condition = $filter->condition(['name' => '']);

        // Empty string is not null, so condition should be returned
        $this->assertIsArray($condition);
        $this->assertSame('%', $condition['where'][2]);
    }

    private function assertConditionHasWhere(mixed $condition): void
    {
        $this->assertIsArray($condition);
        $this->assertContains('where', array_keys($condition));
    }
}
