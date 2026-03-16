<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Filter;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Filter;
use Dcat\Admin\Grid\Filter\FindInSet;
use Dcat\Admin\Tests\TestCase;

class FindInSetTest extends TestCase
{
    protected function makeFilter(string $column, string $label = ''): FindInSet
    {
        $filter = new FindInSet($column, $label);

        $grid = $this->createMock(Grid::class);
        $grid->method('makeName')->willReturnCallback(fn ($name) => $name);

        $parentFilter = $this->createMock(Filter::class);
        $parentFilter->method('grid')->willReturn($grid);

        $filter->setParent($parentFilter);

        return $filter;
    }

    public function test_constructor_sets_column_and_label(): void
    {
        $filter = new FindInSet('tags', 'Tags');
        $this->assertSame('tags', $filter->originalColumn());
        $this->assertSame('Tags', $filter->getLabel());
    }

    public function test_condition_returns_null_when_value_is_null(): void
    {
        $filter = $this->makeFilter('tags');
        $result = $filter->condition(['other' => 'test']);

        $this->assertNull($result);
    }

    public function test_condition_returns_where_with_closure(): void
    {
        $filter = $this->makeFilter('tags');
        $condition = $filter->condition(['tags' => 'php']);

        $this->assertConditionHasWhere($condition);
        $this->assertIsCallable($condition['where'][0]);
    }

    public function test_condition_sets_value_and_input(): void
    {
        $filter = $this->makeFilter('tags');
        $filter->condition(['tags' => 'laravel']);

        $this->assertSame('laravel', $filter->getValue());
        $this->assertSame('laravel', $filter->input);
    }

    public function test_condition_with_numeric_value(): void
    {
        $filter = $this->makeFilter('category_ids');
        $condition = $filter->condition(['category_ids' => '5']);

        $this->assertConditionHasWhere($condition);
    }

    public function test_condition_with_empty_string_returns_where(): void
    {
        $filter = $this->makeFilter('tags');
        $condition = $filter->condition(['tags' => '']);

        // Empty string is not null, condition should still be built
        $this->assertConditionHasWhere($condition);
    }

    private function assertConditionHasWhere(mixed $condition): void
    {
        $this->assertIsArray($condition);
        $this->assertContains('where', array_keys($condition));
    }
}
