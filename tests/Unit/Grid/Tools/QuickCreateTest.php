<?php

namespace Dcat\Admin\Tests\Unit\Grid\Tools;

use Dcat\Admin\Form\Field;
use Dcat\Admin\Form\Field\MultipleSelect;
use Dcat\Admin\Form\Field\Select;
use Dcat\Admin\Form\Field\Text;
use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Tools\QuickCreate;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Support\Collection;
use Mockery;

class QuickCreateTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function createMockGrid(): Grid
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('makeName')->andReturnUsing(function ($key) {
            return 'grid-'.$key;
        });
        $grid->shouldReceive('getName')->andReturn('test-grid');

        return $grid;
    }

    protected function createQuickCreate(): QuickCreate
    {
        $grid = $this->createMockGrid();

        return new QuickCreate($grid);
    }

    public function test_constructor_initializes_empty_fields_collection(): void
    {
        $qc = $this->createQuickCreate();

        $ref = new \ReflectionProperty($qc, 'fields');
        $ref->setAccessible(true);
        $fields = $ref->getValue($qc);

        $this->assertInstanceOf(Collection::class, $fields);
        $this->assertTrue($fields->isEmpty());
    }

    public function test_constructor_stores_grid_as_parent(): void
    {
        $grid = $this->createMockGrid();
        $qc = new QuickCreate($grid);

        $ref = new \ReflectionProperty($qc, 'parent');
        $ref->setAccessible(true);

        $this->assertSame($grid, $ref->getValue($qc));
    }

    public function test_default_method_is_post(): void
    {
        $qc = $this->createQuickCreate();

        $ref = new \ReflectionProperty($qc, 'method');
        $ref->setAccessible(true);

        $this->assertSame('POST', $ref->getValue($qc));
    }

    public function test_method_sets_value_and_returns_this(): void
    {
        $qc = $this->createQuickCreate();
        $result = $qc->method('PUT');

        $this->assertSame($qc, $result);

        $ref = new \ReflectionProperty($qc, 'method');
        $ref->setAccessible(true);
        $this->assertSame('PUT', $ref->getValue($qc));
    }

    public function test_text_returns_text_field_instance(): void
    {
        $qc = $this->createQuickCreate();
        $field = $qc->text('name', 'Enter name');

        $this->assertInstanceOf(Text::class, $field);
    }

    public function test_text_adds_field_to_collection(): void
    {
        $qc = $this->createQuickCreate();
        $qc->text('name', 'Enter name');

        $ref = new \ReflectionProperty($qc, 'fields');
        $ref->setAccessible(true);
        $fields = $ref->getValue($qc);

        $this->assertCount(1, $fields);
        $this->assertInstanceOf(Text::class, $fields->first());
    }

    public function test_hidden_returns_text_field_with_hidden_attribute(): void
    {
        $qc = $this->createQuickCreate();
        $field = $qc->hidden('secret');

        $this->assertInstanceOf(Text::class, $field);

        $ref = new \ReflectionProperty($qc, 'fields');
        $ref->setAccessible(true);
        $this->assertCount(1, $ref->getValue($qc));
    }

    public function test_select_returns_select_field_instance(): void
    {
        $qc = $this->createQuickCreate();
        $field = $qc->select('category', 'Choose');

        $this->assertInstanceOf(Select::class, $field);
    }

    public function test_multiple_select_returns_multiple_select_instance(): void
    {
        $qc = $this->createQuickCreate();
        $field = $qc->multipleSelect('tags', 'Pick tags');

        $this->assertInstanceOf(MultipleSelect::class, $field);
    }

    public function test_date_returns_date_field_instance(): void
    {
        $qc = $this->createQuickCreate();
        $field = $qc->date('birthday', 'Select date');

        $this->assertInstanceOf(Field\Date::class, $field);
    }

    public function test_tags_returns_tags_field_instance(): void
    {
        $qc = $this->createQuickCreate();
        $field = $qc->tags('labels', 'Add tags');

        $this->assertInstanceOf(Field\Tags::class, $field);
    }

    public function test_multiple_fields_accumulate_in_collection(): void
    {
        $qc = $this->createQuickCreate();
        $qc->text('name', 'Name');
        $qc->select('category', 'Category');
        $qc->date('created_at', 'Date');

        $ref = new \ReflectionProperty($qc, 'fields');
        $ref->setAccessible(true);
        $fields = $ref->getValue($qc);

        $this->assertCount(3, $fields);
        $this->assertInstanceOf(Text::class, $fields[0]);
        $this->assertInstanceOf(Select::class, $fields[1]);
        $this->assertInstanceOf(Field\Date::class, $fields[2]);
    }

    public function test_get_element_class_uses_grid_make_name(): void
    {
        $qc = $this->createQuickCreate();
        $class = $qc->getElementClass();

        $this->assertSame('grid-quick-create', $class);
    }

    public function test_render_returns_empty_string_when_no_fields(): void
    {
        $qc = $this->createQuickCreate();
        $result = $qc->render(0);

        $this->assertSame('', $result);
    }

    public function test_resolve_view_maps_class_to_view_name(): void
    {
        $qc = $this->createQuickCreate();

        $ref = new \ReflectionMethod($qc, 'resolveView');
        $ref->setAccessible(true);

        $this->assertSame('admin::grid.quick-create.text', $ref->invoke($qc, Text::class));
        $this->assertSame('admin::grid.quick-create.select', $ref->invoke($qc, Select::class));
        $this->assertSame('admin::grid.quick-create.multipleselect', $ref->invoke($qc, MultipleSelect::class));
        $this->assertSame('admin::grid.quick-create.date', $ref->invoke($qc, Field\Date::class));
    }

    public function test_format_placeholder_filters_empty_values(): void
    {
        $qc = $this->createQuickCreate();

        $ref = new \ReflectionMethod($qc, 'formatPlaceholder');
        $ref->setAccessible(true);

        $this->assertSame(['test'], $ref->invoke($qc, 'test'));
        $this->assertSame([], $ref->invoke($qc, ''));
        $this->assertSame([], $ref->invoke($qc, null));
        $this->assertSame(['Name'], array_values($ref->invoke($qc, ['Name', '', null])));
    }

    public function test_action_sets_url_and_returns_this(): void
    {
        $qc = $this->createQuickCreate();
        $result = $qc->action('/admin/custom-endpoint');

        $this->assertSame($qc, $result);
    }

    public function test_email_returns_text_field(): void
    {
        $qc = $this->createQuickCreate();
        $field = $qc->email('email', 'Email address');

        $this->assertInstanceOf(Text::class, $field);

        $ref = new \ReflectionProperty($qc, 'fields');
        $ref->setAccessible(true);
        $this->assertCount(1, $ref->getValue($qc));
    }

    public function test_ip_returns_text_field(): void
    {
        $qc = $this->createQuickCreate();
        $field = $qc->ip('ip_address', 'IP');

        $this->assertInstanceOf(Text::class, $field);
    }

    public function test_url_returns_text_field(): void
    {
        $qc = $this->createQuickCreate();
        $field = $qc->url('website', 'URL');

        $this->assertInstanceOf(Text::class, $field);
    }

    public function test_password_returns_text_field(): void
    {
        $qc = $this->createQuickCreate();
        $field = $qc->password('pwd', 'Password');

        $this->assertInstanceOf(Text::class, $field);
    }

    public function test_mobile_returns_text_field(): void
    {
        $qc = $this->createQuickCreate();
        $field = $qc->mobile('phone', 'Phone');

        $this->assertInstanceOf(Text::class, $field);
    }

    public function test_integer_returns_text_field(): void
    {
        $qc = $this->createQuickCreate();
        $field = $qc->integer('age', 'Age');

        $this->assertInstanceOf(Text::class, $field);
    }

    public function test_datetime_returns_date_field(): void
    {
        $qc = $this->createQuickCreate();
        $field = $qc->datetime('created_at', 'Created');

        $this->assertInstanceOf(Field\Date::class, $field);
    }

    public function test_time_returns_date_field(): void
    {
        $qc = $this->createQuickCreate();
        $field = $qc->time('start_time', 'Start');

        $this->assertInstanceOf(Field\Date::class, $field);
    }
}
