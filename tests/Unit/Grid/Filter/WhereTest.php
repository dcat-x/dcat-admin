<?php

namespace Dcat\Admin\Tests\Unit\Grid\Filter;

use Dcat\Admin\Grid\Filter\Where;
use Dcat\Admin\Tests\TestCase;

class WhereTest extends TestCase
{
    protected function makeFilter(string $column, \Closure $query, string $label = ''): Where
    {
        $filter = new Where($column, $query, $label);

        $grid = $this->createMock(\Dcat\Admin\Grid::class);
        $grid->method('makeName')->willReturnCallback(fn ($name) => $name);

        $parentFilter = $this->createMock(\Dcat\Admin\Grid\Filter::class);
        $parentFilter->method('grid')->willReturn($grid);

        $filter->setParent($parentFilter);

        return $filter;
    }

    public function test_constructor_sets_column_and_label(): void
    {
        $query = function ($q) {};
        $filter = new Where('keyword', $query, 'Keyword');

        $this->assertSame('keyword', $filter->originalColumn());
        $this->assertSame('Keyword', $filter->getLabel());
    }

    public function test_condition_returns_null_when_value_is_null(): void
    {
        $query = function ($q) {};
        $filter = $this->makeFilter('keyword', $query);

        $result = $filter->condition(['other' => 'test']);
        $this->assertNull($result);
    }

    public function test_condition_returns_where_with_closure(): void
    {
        $query = function ($q) {
            $q->where('name', 'like', "%{$this->input}%");
        };

        $filter = $this->makeFilter('keyword', $query);
        $condition = $filter->condition(['keyword' => 'test']);

        $this->assertConditionHasWhere($condition);
        $this->assertIsCallable($condition['where'][0]);
    }

    public function test_condition_sets_value_and_input(): void
    {
        $query = function ($q) {};
        $filter = $this->makeFilter('keyword', $query);
        $filter->condition(['keyword' => 'hello']);

        $this->assertSame('hello', $filter->getValue());
        $this->assertSame('hello', $filter->input);
    }

    public function test_condition_with_complex_closure(): void
    {
        $query = function ($q) {
            $q->where('title', 'like', "%{$this->input}%")
                ->orWhere('content', 'like', "%{$this->input}%");
        };

        $filter = $this->makeFilter('search', $query);
        $condition = $filter->condition(['search' => 'hello']);

        $this->assertConditionHasWhere($condition);
    }

    public function test_closure_is_bound_to_filter_instance(): void
    {
        $capturedInput = null;
        $query = function ($q) use (&$capturedInput) {
            $capturedInput = $this->input;
        };

        $filter = $this->makeFilter('keyword', $query);
        $condition = $filter->condition(['keyword' => 'bound_test']);

        // Execute the closure to verify binding
        $this->assertConditionHasWhere($condition);
        $closure = $condition['where'][0];

        $mockQuery = $this->createMock(\Illuminate\Database\Query\Builder::class);
        $closure($mockQuery);

        $this->assertSame('bound_test', $capturedInput);
    }

    private function assertConditionHasWhere(mixed $condition): void
    {
        $this->assertIsArray($condition);
        $this->assertContains('where', array_keys($condition));
    }
}
