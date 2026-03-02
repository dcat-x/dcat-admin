<?php

namespace Dcat\Admin\Tests\Unit\Grid\Filter;

use Dcat\Admin\Grid\Filter\AbstractFilter;
use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Traits\HasVariables;
use Mockery;

class AbstractFilterTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function makeFilter(string $column = 'test_col', string $label = 'Test Label'): AbstractFilter
    {
        return new class($column, $label) extends AbstractFilter {};
    }

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(AbstractFilter::class));
    }

    public function test_is_abstract(): void
    {
        $ref = new \ReflectionClass(AbstractFilter::class);

        $this->assertTrue($ref->isAbstract());
    }

    public function test_uses_has_variables_trait(): void
    {
        $ref = new \ReflectionClass(AbstractFilter::class);
        $traits = $ref->getTraitNames();

        $this->assertContains(HasVariables::class, $traits);
    }

    public function test_query_default_is_where(): void
    {
        $ref = new \ReflectionProperty(AbstractFilter::class, 'query');
        $ref->setAccessible(true);

        $this->assertSame('where', $ref->getDefaultValue());
    }

    public function test_width_default_is_10(): void
    {
        $ref = new \ReflectionProperty(AbstractFilter::class, 'width');
        $ref->setAccessible(true);

        $this->assertSame(10, $ref->getDefaultValue());
    }

    public function test_view_default_is_admin_filter_where(): void
    {
        $ref = new \ReflectionProperty(AbstractFilter::class, 'view');
        $ref->setAccessible(true);

        $this->assertSame('admin::filter.where', $ref->getDefaultValue());
    }

    public function test_ignore_default_is_false(): void
    {
        $ref = new \ReflectionProperty(AbstractFilter::class, 'ignore');
        $ref->setAccessible(true);

        $this->assertFalse($ref->getDefaultValue());
    }

    public function test_method_width_exists(): void
    {
        $this->assertTrue(method_exists(AbstractFilter::class, 'width'));
    }

    public function test_method_set_parent_exists(): void
    {
        $this->assertTrue(method_exists(AbstractFilter::class, 'setParent'));
    }

    public function test_method_parent_exists(): void
    {
        $this->assertTrue(method_exists(AbstractFilter::class, 'parent'));
    }

    public function test_method_siblings_exists(): void
    {
        $this->assertTrue(method_exists(AbstractFilter::class, 'siblings'));
    }

    public function test_method_previous_exists(): void
    {
        $this->assertTrue(method_exists(AbstractFilter::class, 'previous'));
    }

    public function test_method_next_exists(): void
    {
        $this->assertTrue(method_exists(AbstractFilter::class, 'next'));
    }

    public function test_method_condition_exists(): void
    {
        $this->assertTrue(method_exists(AbstractFilter::class, 'condition'));
    }

    public function test_method_ignore_exists(): void
    {
        $this->assertTrue(method_exists(AbstractFilter::class, 'ignore'));
    }

    public function test_method_select_exists(): void
    {
        $this->assertTrue(method_exists(AbstractFilter::class, 'select'));
    }

    public function test_method_multiple_select_exists(): void
    {
        $this->assertTrue(method_exists(AbstractFilter::class, 'multipleSelect'));
    }

    public function test_method_radio_exists(): void
    {
        $this->assertTrue(method_exists(AbstractFilter::class, 'radio'));
    }

    public function test_method_checkbox_exists(): void
    {
        $this->assertTrue(method_exists(AbstractFilter::class, 'checkbox'));
    }

    public function test_method_datetime_exists(): void
    {
        $this->assertTrue(method_exists(AbstractFilter::class, 'datetime'));
    }

    public function test_method_date_exists(): void
    {
        $this->assertTrue(method_exists(AbstractFilter::class, 'date'));
    }

    public function test_method_time_exists(): void
    {
        $this->assertTrue(method_exists(AbstractFilter::class, 'time'));
    }

    public function test_method_day_exists(): void
    {
        $this->assertTrue(method_exists(AbstractFilter::class, 'day'));
    }

    public function test_method_month_exists(): void
    {
        $this->assertTrue(method_exists(AbstractFilter::class, 'month'));
    }

    public function test_method_year_exists(): void
    {
        $this->assertTrue(method_exists(AbstractFilter::class, 'year'));
    }

    public function test_method_set_presenter_exists(): void
    {
        $this->assertTrue(method_exists(AbstractFilter::class, 'setPresenter'));
    }

    public function test_method_default_exists(): void
    {
        $this->assertTrue(method_exists(AbstractFilter::class, 'default'));
    }

    public function test_method_get_default_exists(): void
    {
        $this->assertTrue(method_exists(AbstractFilter::class, 'getDefault'));
    }

    public function test_method_get_id_exists(): void
    {
        $this->assertTrue(method_exists(AbstractFilter::class, 'getId'));
    }

    public function test_method_set_id_exists(): void
    {
        $this->assertTrue(method_exists(AbstractFilter::class, 'setId'));
    }

    public function test_method_column_exists(): void
    {
        $this->assertTrue(method_exists(AbstractFilter::class, 'column'));
    }

    public function test_method_original_column_exists(): void
    {
        $this->assertTrue(method_exists(AbstractFilter::class, 'originalColumn'));
    }

    public function test_method_get_label_exists(): void
    {
        $this->assertTrue(method_exists(AbstractFilter::class, 'getLabel'));
    }

    public function test_method_get_value_exists(): void
    {
        $this->assertTrue(method_exists(AbstractFilter::class, 'getValue'));
    }

    public function test_method_set_value_exists(): void
    {
        $this->assertTrue(method_exists(AbstractFilter::class, 'setValue'));
    }

    public function test_method_render_exists(): void
    {
        $this->assertTrue(method_exists(AbstractFilter::class, 'render'));
    }

    public function test_constructor_sets_column(): void
    {
        $filter = $this->makeFilter('test_col', 'Test Label');

        $this->assertSame('test_col', $filter->originalColumn());
    }

    public function test_constructor_sets_label(): void
    {
        $filter = $this->makeFilter('test_col', 'Test Label');

        $this->assertSame('Test Label', $filter->getLabel());
    }

    public function test_original_column_returns_test_col(): void
    {
        $filter = $this->makeFilter('test_col', 'Test Label');

        $this->assertSame('test_col', $filter->originalColumn());
    }

    public function test_get_label_returns_test_label(): void
    {
        $filter = $this->makeFilter('test_col', 'Test Label');

        $this->assertSame('Test Label', $filter->getLabel());
    }

    public function test_ignore_sets_ignore_and_returns_this(): void
    {
        $filter = $this->makeFilter();

        $result = $filter->ignore();

        $this->assertSame($filter, $result);

        $ref = new \ReflectionProperty($filter, 'ignore');
        $ref->setAccessible(true);

        $this->assertTrue($ref->getValue($filter));
    }

    public function test_set_value_then_get_value(): void
    {
        $filter = $this->makeFilter();

        $filter->setValue('foo');

        $this->assertSame('foo', $filter->getValue());
    }

    public function test_default_then_get_default(): void
    {
        $filter = $this->makeFilter();

        $filter->default('bar');

        $this->assertSame('bar', $filter->getDefault());
    }

    public function test_width_with_integer_sets_width(): void
    {
        $filter = $this->makeFilter();

        $result = $filter->width(6);

        $this->assertSame($filter, $result);

        $ref = new \ReflectionProperty($filter, 'width');
        $ref->setAccessible(true);

        $this->assertSame(6, $ref->getValue($filter));
    }

    public function test_width_with_string_sets_style(): void
    {
        $filter = $this->makeFilter();

        $result = $filter->width('200px');

        $this->assertSame($filter, $result);

        $ref = new \ReflectionProperty($filter, 'style');
        $ref->setAccessible(true);

        $this->assertSame('width:200px;padding-left:10px;padding-right:10px', $ref->getValue($filter));
    }

    public function test_width_with_string_sets_width_to_space(): void
    {
        $filter = $this->makeFilter();

        $filter->width('200px');

        $ref = new \ReflectionProperty($filter, 'width');
        $ref->setAccessible(true);

        $this->assertSame(' ', $ref->getValue($filter));
    }

    public function test_set_value_returns_this(): void
    {
        $filter = $this->makeFilter();

        $result = $filter->setValue('value');

        $this->assertSame($filter, $result);
    }

    public function test_default_returns_this(): void
    {
        $filter = $this->makeFilter();

        $result = $filter->default('value');

        $this->assertSame($filter, $result);
    }

    public function test_get_default_returns_null_initially(): void
    {
        $filter = $this->makeFilter();

        $this->assertNull($filter->getDefault());
    }

    public function test_get_value_returns_null_initially(): void
    {
        $filter = $this->makeFilter();

        $this->assertNull($filter->getValue());
    }
}
