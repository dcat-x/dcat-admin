<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Filter;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Filter;
use Dcat\Admin\Grid\Filter\Like;
use Dcat\Admin\Tests\TestCase;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

#[AllowMockObjectsWithoutExpectations]
class LikeTest extends TestCase
{
    protected function makeFilter(string $column, string $label = ''): Like
    {
        $filter = new Like($column, $label);

        $grid = $this->createMock(Grid::class);
        $grid->method('makeName')->willReturnCallback(fn ($name) => $name);

        $parentFilter = $this->createMock(Filter::class);
        $parentFilter->method('grid')->willReturn($grid);

        $filter->setParent($parentFilter);

        return $filter;
    }

    public function test_constructor_sets_column_and_label(): void
    {
        $filter = new Like('name', 'Name');

        $this->assertSame('name', $filter->originalColumn());
        $this->assertSame('Name', $filter->getLabel());
    }

    public function test_condition_returns_where_with_like_operator(): void
    {
        $filter = $this->makeFilter('name', 'Name');

        $condition = $filter->condition(['name' => 'john']);

        $this->assertConditionHasWhere($condition);
        $this->assertSame(['name', 'like', '%john%'], $condition['where']);
    }

    public function test_condition_wraps_value_with_wildcards(): void
    {
        $filter = $this->makeFilter('title', 'Title');

        $condition = $filter->condition(['title' => 'test']);

        $this->assertSame('%test%', $condition['where'][2]);
    }

    public function test_condition_returns_null_when_value_is_null(): void
    {
        $filter = $this->makeFilter('name', 'Name');

        $condition = $filter->condition(['other' => 'value']);

        $this->assertNull($condition);
    }

    public function test_condition_with_empty_string(): void
    {
        $filter = $this->makeFilter('name', 'Name');

        $condition = $filter->condition(['name' => '']);

        $this->assertConditionHasWhere($condition);
        $this->assertSame(['name', 'like', '%%'], $condition['where']);
    }

    public function test_condition_sets_value_on_filter(): void
    {
        $filter = $this->makeFilter('name', 'Name');

        $filter->condition(['name' => 'search_term']);

        $this->assertSame('search_term', $filter->getValue());
    }

    public function test_ignore_returns_null_condition(): void
    {
        $filter = $this->makeFilter('name', 'Name');
        $filter->ignore();

        $condition = $filter->condition(['name' => 'john']);

        $this->assertNull($condition);
    }

    private function assertConditionHasWhere(mixed $condition): void
    {
        $this->assertIsArray($condition);
        $this->assertContains('where', array_keys($condition));
    }
}
