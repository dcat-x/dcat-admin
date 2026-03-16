<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Filter;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Filter;
use Dcat\Admin\Grid\Filter\Group;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Support\Collection;

class GroupTest extends TestCase
{
    protected function makeFilter(string $column, ?\Closure $builder = null, string $label = ''): Group
    {
        $filter = new Group($column, $builder, $label);

        $grid = $this->createMock(Grid::class);
        $grid->method('makeName')->willReturnCallback(fn ($name) => $name);
        $grid->method('getNamePrefix')->willReturn('');

        $parentFilter = $this->createMock(Filter::class);
        $parentFilter->method('grid')->willReturn($grid);

        $filter->setParent($parentFilter);

        return $filter;
    }

    public function test_constructor_sets_column_and_label(): void
    {
        $filter = new Group('price', null, 'Price');
        $this->assertSame('Price', $filter->getLabel());
    }

    public function test_set_parent_initializes_group(): void
    {
        $filter = $this->makeFilter('price');
        $this->assertInstanceOf(Collection::class, $filter->group);
        $this->assertTrue($filter->group->isEmpty());
    }

    public function test_equal_adds_group_condition(): void
    {
        $filter = $this->makeFilter('price');
        $filter->equal('Equal');

        $this->assertCount(1, $filter->group);
        $item = $filter->group->first();
        $this->assertSame('Equal', $item['label']);
        $this->assertSame('price', $item['condition'][0]);
        $this->assertSame('=', $item['condition'][1]);
    }

    public function test_not_equal_adds_group_condition(): void
    {
        $filter = $this->makeFilter('price');
        $filter->notEqual('Not Equal');

        $item = $filter->group->first();
        $this->assertSame('Not Equal', $item['label']);
        $this->assertSame('!=', $item['condition'][1]);
    }

    public function test_gt_adds_greater_than_condition(): void
    {
        $filter = $this->makeFilter('price');
        $filter->gt('Greater Than');

        $item = $filter->group->first();
        $this->assertSame('>', $item['condition'][1]);
    }

    public function test_lt_adds_less_than_condition(): void
    {
        $filter = $this->makeFilter('price');
        $filter->lt('Less Than');

        $item = $filter->group->first();
        $this->assertSame('<', $item['condition'][1]);
    }

    public function test_nlt_adds_not_less_than_condition(): void
    {
        $filter = $this->makeFilter('price');
        $filter->nlt('Not Less');

        $item = $filter->group->first();
        $this->assertSame('>=', $item['condition'][1]);
    }

    public function test_ngt_adds_not_greater_than_condition(): void
    {
        $filter = $this->makeFilter('price');
        $filter->ngt('Not Greater');

        $item = $filter->group->first();
        $this->assertSame('<=', $item['condition'][1]);
    }

    public function test_match_adds_regexp_condition(): void
    {
        $filter = $this->makeFilter('name');
        $filter->match('Regex');

        $item = $filter->group->first();
        $this->assertSame('Regex', $item['label']);
        $this->assertSame('REGEXP', $item['condition'][1]);
    }

    public function test_like_adds_like_condition(): void
    {
        $filter = $this->makeFilter('name');
        $filter->like('Contains');

        $item = $filter->group->first();
        $this->assertSame('Contains', $item['label']);
        $this->assertSame('like', $item['condition'][1]);
    }

    public function test_contains_is_alias_of_like(): void
    {
        $filter = $this->makeFilter('name');
        $filter->contains('Contains');

        $item = $filter->group->first();
        $this->assertSame('like', $item['condition'][1]);
    }

    public function test_ilike_adds_ilike_condition(): void
    {
        $filter = $this->makeFilter('name');
        $filter->ilike('Case Insensitive');

        $item = $filter->group->first();
        $this->assertSame('ilike', $item['condition'][1]);
    }

    public function test_start_with_adds_start_like_condition(): void
    {
        $filter = $this->makeFilter('name');
        $filter->startWith('Starts');

        $item = $filter->group->first();
        $this->assertSame('Starts', $item['label']);
        $this->assertSame('like', $item['condition'][1]);
    }

    public function test_end_with_adds_end_like_condition(): void
    {
        $filter = $this->makeFilter('name');
        $filter->endWith('Ends');

        $item = $filter->group->first();
        $this->assertSame('Ends', $item['label']);
        $this->assertSame('like', $item['condition'][1]);
    }

    public function test_condition_returns_null_when_value_not_set(): void
    {
        $filter = $this->makeFilter('price');
        $result = $filter->condition(['other' => '10']);

        $this->assertNull($result);
    }

    public function test_condition_with_builder_callback(): void
    {
        $builder = function (Group $group) {
            $group->equal('Equal');
            $group->gt('Greater');
        };

        $filter = $this->makeFilter('price', $builder);

        // Select the first group (index 0 = equal)
        $condition = $filter->condition([
            'price' => '100',
            'filter-column-price_group' => 0,
        ]);

        $this->assertConditionHasWhere($condition);
        $this->assertSame('price', $condition['where'][0]);
        $this->assertSame('=', $condition['where'][1]);
    }

    public function test_condition_with_second_group_selected(): void
    {
        $builder = function (Group $group) {
            $group->equal('Equal');
            $group->gt('Greater');
        };

        $filter = $this->makeFilter('price', $builder);

        $condition = $filter->condition([
            'price' => '100',
            'filter-column-price_group' => 1,
        ]);

        $this->assertConditionHasWhere($condition);
        $this->assertSame('>', $condition['where'][1]);
    }

    public function test_condition_returns_null_for_invalid_group_index(): void
    {
        $builder = function (Group $group) {
            $group->equal('Equal');
        };

        $filter = $this->makeFilter('price', $builder);

        $condition = $filter->condition([
            'price' => '100',
            'filter-column-price_group' => 99,
        ]);

        $this->assertNull($condition);
    }

    public function test_multiple_groups_are_collected(): void
    {
        $filter = $this->makeFilter('amount');
        $filter->equal('Equal');
        $filter->gt('GT');
        $filter->lt('LT');
        $filter->nlt('NLT');
        $filter->ngt('NGT');

        $this->assertCount(5, $filter->group);
    }

    public function test_equal_default_label_uses_operator(): void
    {
        $filter = $this->makeFilter('price');
        $filter->equal();

        $item = $filter->group->first();
        $this->assertSame('=', $item['label']);
    }

    public function test_match_default_label(): void
    {
        $filter = $this->makeFilter('name');
        $filter->match();

        $item = $filter->group->first();
        $this->assertSame('Match', $item['label']);
    }

    private function assertConditionHasWhere(mixed $condition): void
    {
        $this->assertIsArray($condition);
        $this->assertContains('where', array_keys($condition));
    }
}
