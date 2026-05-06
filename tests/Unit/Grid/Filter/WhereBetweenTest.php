<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Filter;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Filter;
use Dcat\Admin\Grid\Filter\WhereBetween;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Database\Query\Builder;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;

#[AllowMockObjectsWithoutExpectations]
class WhereBetweenTest extends TestCase
{
    protected function makeFilter(string $column, \Closure $query, string $label = ''): WhereBetween
    {
        $filter = new WhereBetween($column, $query, $label);

        $grid = $this->createMock(Grid::class);
        $grid->method('makeName')->willReturnCallback(fn ($name) => $name);

        $parentFilter = $this->createMock(Filter::class);
        $parentFilter->method('grid')->willReturn($grid);

        $filter->setParent($parentFilter);

        return $filter;
    }

    public function test_constructor_sets_column_and_label(): void
    {
        $query = function ($q) {};
        $filter = new WhereBetween('price', $query, 'Price Range');

        $this->assertSame('price', $filter->originalColumn());
        $this->assertSame('Price Range', $filter->getLabel());
    }

    public function test_condition_returns_null_when_value_missing(): void
    {
        $query = function ($q) {};
        $filter = $this->makeFilter('price', $query);

        $result = $filter->condition(['other' => 'value']);
        $this->assertNull($result);
    }

    public function test_condition_returns_null_when_value_is_empty_array(): void
    {
        $query = function ($q) {};
        $filter = $this->makeFilter('price', $query);

        $result = $filter->condition(['price' => []]);
        $this->assertNull($result);
    }

    public function test_condition_returns_null_when_no_start_and_no_end(): void
    {
        $query = function ($q) {};
        $filter = $this->makeFilter('price', $query);

        $result = $filter->condition(['price' => ['foo' => 'bar']]);
        $this->assertNull($result);
    }

    public function test_condition_with_start_value(): void
    {
        $query = function ($q) {
            $q->where('price', '>=', $this->input['start']);
        };

        $filter = $this->makeFilter('price', $query);
        $condition = $filter->condition(['price' => ['start' => '100', 'end' => null]]);

        $this->assertConditionHasWhere($condition);
    }

    public function test_condition_with_end_value(): void
    {
        $query = function ($q) {
            $q->where('price', '<=', $this->input['end']);
        };

        $filter = $this->makeFilter('price', $query);
        $condition = $filter->condition(['price' => ['start' => null, 'end' => '500']]);

        $this->assertConditionHasWhere($condition);
    }

    public function test_condition_with_both_start_and_end(): void
    {
        $query = function ($q) {
            $q->whereBetween('price', [$this->input['start'], $this->input['end']]);
        };

        $filter = $this->makeFilter('price', $query);
        $condition = $filter->condition(['price' => ['start' => '100', 'end' => '500']]);

        $this->assertConditionHasWhere($condition);
    }

    public function test_condition_sets_value_and_input(): void
    {
        $query = function ($q) {};
        $filter = $this->makeFilter('price', $query);

        $input = ['start' => '10', 'end' => '100'];
        $filter->condition(['price' => $input]);

        $this->assertSame($input, $filter->getValue());
        $this->assertSame($input, $filter->input);
    }

    public function test_closure_is_bound_to_filter_instance(): void
    {
        $capturedInput = null;
        $query = function ($q) use (&$capturedInput) {
            $capturedInput = $this->input;
        };

        $filter = $this->makeFilter('price', $query);
        $condition = $filter->condition(['price' => ['start' => '50', 'end' => '200']]);

        // Execute the closure to verify binding
        $closure = $condition['where'][0];
        $mockQuery = $this->createMock(Builder::class);
        $closure($mockQuery);

        $this->assertSame(['start' => '50', 'end' => '200'], $capturedInput);
    }

    private function assertConditionHasWhere(mixed $condition): void
    {
        $this->assertIsArray($condition);
        $this->assertContains('where', array_keys($condition));
    }
}
