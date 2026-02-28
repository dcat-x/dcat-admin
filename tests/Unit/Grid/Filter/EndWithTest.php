<?php

namespace Dcat\Admin\Tests\Unit\Grid\Filter;

use Dcat\Admin\Grid\Filter\EndWith;
use Dcat\Admin\Tests\TestCase;

class EndWithTest extends TestCase
{
    protected function makeFilter(string $column, string $label = ''): EndWith
    {
        $filter = new EndWith($column, $label);

        $grid = $this->createMock(\Dcat\Admin\Grid::class);
        $grid->method('makeName')->willReturnCallback(fn ($name) => $name);

        $parentFilter = $this->createMock(\Dcat\Admin\Grid\Filter::class);
        $parentFilter->method('grid')->willReturn($grid);

        $filter->setParent($parentFilter);

        return $filter;
    }

    public function test_constructor_sets_column_and_label(): void
    {
        $filter = new EndWith('email', 'Email');
        $this->assertEquals('email', $filter->originalColumn());
        $this->assertEquals('Email', $filter->getLabel());
    }

    public function test_condition_returns_like_with_leading_wildcard(): void
    {
        $filter = $this->makeFilter('email');
        $condition = $filter->condition(['email' => '.com']);

        $this->assertIsArray($condition);
        $this->assertArrayHasKey('where', $condition);
        $this->assertEquals('email', $condition['where'][0]);
        $this->assertEquals('like', $condition['where'][1]);
        $this->assertEquals('%.com', $condition['where'][2]);
    }

    public function test_condition_returns_null_when_value_is_null(): void
    {
        $filter = $this->makeFilter('email');
        $result = $filter->condition(['other' => 'bar']);

        $this->assertNull($result);
    }

    public function test_ilike_changes_type(): void
    {
        $filter = $this->makeFilter('email');
        $result = $filter->ilike();

        $this->assertInstanceOf(EndWith::class, $result);

        $condition = $filter->condition(['email' => '.org']);

        $this->assertEquals('ilike', $condition['where'][1]);
        $this->assertEquals('%.org', $condition['where'][2]);
    }

    public function test_condition_sets_value(): void
    {
        $filter = $this->makeFilter('email');
        $filter->condition(['email' => '@test.com']);

        $this->assertEquals('@test.com', $filter->getValue());
    }

    public function test_condition_with_empty_string_value(): void
    {
        $filter = $this->makeFilter('email');
        $condition = $filter->condition(['email' => '']);

        $this->assertIsArray($condition);
        $this->assertEquals('%', $condition['where'][2]);
    }
}
