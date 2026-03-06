<?php

namespace Dcat\Admin\Tests\Unit\Grid\Filter;

use Dcat\Admin\Exception\RuntimeException;
use Dcat\Admin\Grid\Filter;
use Dcat\Admin\Grid\Filter\AbstractFilter;
use Dcat\Admin\Grid\Filter\Presenter\BatchInput;
use Dcat\Admin\Grid\Filter\Presenter\Checkbox;
use Dcat\Admin\Grid\Filter\Presenter\DateTime;
use Dcat\Admin\Grid\Filter\Presenter\MultipleSelect;
use Dcat\Admin\Grid\Filter\Presenter\Radio;
use Dcat\Admin\Grid\Filter\Presenter\Select;
use Dcat\Admin\Grid\Filter\Presenter\Text;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class AbstractFilterTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function makeFilter(string $column = 'name', string $label = 'Name'): AbstractFilter
    {
        return new class($column, $label) extends AbstractFilter {};
    }

    protected function makeParent(array $filters = [], array $inputs = []): Filter
    {
        $grid = new class
        {
            public function makeName(string $name): string
            {
                return 'admin_'.$name;
            }
        };

        $parent = Mockery::mock(Filter::class);
        $parent->shouldReceive('grid')->andReturn($grid);
        $parent->shouldReceive('filters')->andReturn($filters);
        $parent->shouldReceive('inputs')->andReturn($inputs);

        return $parent;
    }

    public function test_constructor_sets_column_and_label(): void
    {
        $filter = $this->makeFilter('user.name', 'User Name');

        $this->assertSame('user.name', $filter->originalColumn());
        $this->assertSame('User Name', $filter->getLabel());
    }

    public function test_set_parent_builds_id_and_parent_accessors(): void
    {
        $filter = $this->makeFilter('profile.name', 'Profile Name');
        $parent = $this->makeParent();

        $filter->setParent($parent);

        $this->assertSame($parent, $filter->parent());
        $this->assertSame('admin_filter-column-profile-name', $filter->getId());
    }

    public function test_set_id_and_column_use_formatted_grid_name(): void
    {
        $filter = $this->makeFilter('profile.name', 'Profile Name');
        $filter->setParent($this->makeParent());
        $filter->setId('custom.id');

        $this->assertSame('admin_filter-column-custom-id', $filter->getId());
        $this->assertSame('admin_profile-name', $filter->column());
        $this->assertSame('admin_profile.name', $filter->getElementName());
    }

    public function test_siblings_previous_and_next_follow_parent_filters_order(): void
    {
        $first = $this->makeFilter('first', 'First');
        $current = $this->makeFilter('current', 'Current');
        $last = $this->makeFilter('last', 'Last');

        $filters = [$first, $current, $last];
        $parent = $this->makeParent($filters);

        $first->setParent($parent);
        $current->setParent($parent);
        $last->setParent($parent);

        $this->assertSame($filters, $current->siblings());
        $this->assertSame($first, $current->previous());
        $this->assertSame($last, $current->next());
    }

    public function test_condition_builds_where_for_single_column(): void
    {
        $filter = $this->makeFilter('name', 'Name');

        $condition = $filter->condition(['name' => 'Taylor']);

        $this->assertSame('Taylor', $filter->getValue());
        $this->assertIsArray($condition);
        $this->assertIsArray($condition['where'] ?? null);
        $this->assertSame('name', $condition['where'][0]);
        $this->assertSame('Taylor', $condition['where'][1]);
    }

    public function test_ignore_prevents_condition_building(): void
    {
        $filter = $this->makeFilter('name', 'Name');
        $filter->ignore();

        $this->assertNull($filter->condition(['name' => 'Taylor']));
    }

    public function test_condition_builds_relation_query_for_dotted_column(): void
    {
        $filter = $this->makeFilter('profile.name', 'Profile Name');

        $condition = $filter->condition(['profile.name' => 'Taylor']);

        $method = array_key_first($condition);

        $this->assertContains($method, ['whereHas', 'whereHasIn']);
        $this->assertSame('profile', $condition[$method][0]);
        $this->assertIsCallable($condition[$method][1]);

        $query = Mockery::mock();
        $query->shouldReceive('getModel')->andReturn(new class
        {
            public function getTable(): string
            {
                return 'profiles';
            }
        });
        $query->shouldReceive('where')->once()->with('profiles.name', 'Taylor');

        $condition[$method][1]($query);
    }

    public function test_default_and_value_helpers_work_as_expected(): void
    {
        $filter = $this->makeFilter();

        $this->assertNull($filter->getDefault());
        $this->assertNull($filter->getValue());

        $filter->default('guest')->setValue('admin');

        $this->assertSame('guest', $filter->getDefault());
        $this->assertSame('admin', $filter->getValue());
    }

    public function test_width_accepts_numeric_and_string_styles(): void
    {
        $filter = $this->makeFilter();
        $filter->setParent($this->makeParent());

        $filter->width(6);
        $numericHtml = $filter->render();
        $this->assertStringContainsString('col-sm-6', $numericHtml);

        $filter->width('200px');
        $styledHtml = $filter->render();
        $this->assertStringContainsString('col-sm- ', $styledHtml);
        $this->assertStringContainsString('width:200px', $styledHtml);
    }

    public function test_presenter_factory_methods_return_expected_presenter_types(): void
    {
        $filter = $this->makeFilter();

        $this->assertInstanceOf(Select::class, $filter->select([]));
        $this->assertInstanceOf(MultipleSelect::class, $filter->multipleSelect([]));
        $this->assertInstanceOf(Radio::class, $filter->radio([]));
        $this->assertInstanceOf(Checkbox::class, $filter->checkbox([]));
        $this->assertInstanceOf(DateTime::class, $filter->datetime([]));
        $this->assertInstanceOf(DateTime::class, $filter->date());
        $this->assertInstanceOf(DateTime::class, $filter->time());
        $this->assertInstanceOf(DateTime::class, $filter->day());
        $this->assertInstanceOf(DateTime::class, $filter->month());
        $this->assertInstanceOf(DateTime::class, $filter->year());
        $this->assertInstanceOf(BatchInput::class, $filter->batchInput('/lookup'));
    }

    public function test_render_returns_filter_markup_with_presenter_output(): void
    {
        $filter = $this->makeFilter('name', 'Name');
        $filter->setParent($this->makeParent([], ['name' => 'Taylor']));
        $filter->setPresenter(new Text('Name'));

        $html = $filter->render();

        $this->assertStringContainsString('filter-input', $html);
        $this->assertStringContainsString('input-group', $html);
        $this->assertStringContainsString('admin_name', $html);
        $this->assertStringContainsString('Taylor', $html);
    }

    public function test_call_delegates_to_presenter_method(): void
    {
        $filter = $this->makeFilter('name', 'Name');

        $presenter = $filter->placeholder('Search...');

        $this->assertInstanceOf(Text::class, $presenter);
    }

    public function test_call_throws_runtime_exception_for_unknown_method(): void
    {
        $filter = $this->makeFilter('name', 'Name');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Call to undefined method');

        $filter->methodDoesNotExist();
    }
}
