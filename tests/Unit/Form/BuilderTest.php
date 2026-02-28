<?php

namespace Dcat\Admin\Tests\Unit\Form;

use Dcat\Admin\Form;
use Dcat\Admin\Form\Builder;
use Dcat\Admin\Form\Field;
use Dcat\Admin\Form\Footer;
use Dcat\Admin\Form\Tools;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class BuilderTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function createBuilder(): Builder
    {
        $form = Mockery::mock(Form::class);
        $form->shouldReceive('rows')->andReturn([]);
        $form->shouldReceive('getTab')->andReturn(
            Mockery::mock()->shouldReceive('isEmpty')->andReturn(true)->getMock()
        );
        $form->shouldReceive('resource')->andReturn('/admin/users');
        $form->shouldReceive('keyName')->andReturn('id');
        $form->shouldReceive('createdAtColumn')->andReturn('created_at');
        $form->shouldReceive('updatedAtColumn')->andReturn('updated_at');

        return new Builder($form);
    }

    public function test_mode_constants(): void
    {
        $this->assertSame('edit', Builder::MODE_EDIT);
        $this->assertSame('create', Builder::MODE_CREATE);
        $this->assertSame('delete', Builder::MODE_DELETE);
    }

    public function test_previous_url_key_constant(): void
    {
        $this->assertSame('_previous_', Builder::PREVIOUS_URL_KEY);
    }

    public function test_default_mode_is_create(): void
    {
        $builder = $this->createBuilder();

        $this->assertSame(Builder::MODE_CREATE, $builder->mode());
    }

    public function test_mode_setter_and_getter(): void
    {
        $builder = $this->createBuilder();

        $builder->mode(Builder::MODE_EDIT);
        $this->assertSame(Builder::MODE_EDIT, $builder->mode());

        $builder->mode(Builder::MODE_DELETE);
        $this->assertSame(Builder::MODE_DELETE, $builder->mode());
    }

    public function test_is_creating_returns_true_by_default(): void
    {
        $builder = $this->createBuilder();

        $this->assertTrue($builder->isCreating());
    }

    public function test_is_editing_returns_false_by_default(): void
    {
        $builder = $this->createBuilder();

        $this->assertFalse($builder->isEditing());
    }

    public function test_is_deleting_returns_false_by_default(): void
    {
        $builder = $this->createBuilder();

        $this->assertFalse($builder->isDeleting());
    }

    public function test_set_and_get_resource_id(): void
    {
        $builder = $this->createBuilder();

        $this->assertNull($builder->getResourceId());

        $builder->setResourceId(42);
        $this->assertSame(42, $builder->getResourceId());

        $builder->setResourceId('abc');
        $this->assertSame('abc', $builder->getResourceId());
    }

    public function test_default_width(): void
    {
        $builder = $this->createBuilder();

        $width = $builder->getWidth();
        $this->assertSame(['label' => 2, 'field' => 8], $width);
    }

    public function test_width_setter_returns_this(): void
    {
        $builder = $this->createBuilder();

        $result = $builder->width(10, 4);
        $this->assertSame($builder, $result);
    }

    public function test_width_sets_values(): void
    {
        $builder = $this->createBuilder();

        $builder->width(10, 4);
        $width = $builder->getWidth();

        $this->assertSame(['label' => 4, 'field' => 10], $width);
    }

    public function test_title_getter_returns_create_by_default(): void
    {
        $builder = $this->createBuilder();

        $this->assertSame(trans('admin.create'), $builder->title());
    }

    public function test_title_setter(): void
    {
        $builder = $this->createBuilder();

        $result = $builder->title('My Custom Title');
        $this->assertSame($builder, $result);
        $this->assertSame('My Custom Title', $builder->title());
    }

    public function test_view_sets_view_and_returns_this(): void
    {
        $builder = $this->createBuilder();

        $result = $builder->view('custom.view');
        $this->assertSame($builder, $result);

        $ref = new \ReflectionProperty($builder, 'view');
        $ref->setAccessible(true);
        $this->assertSame('custom.view', $ref->getValue($builder));
    }

    public function test_tools_returns_tools_instance(): void
    {
        $builder = $this->createBuilder();

        $this->assertInstanceOf(Tools::class, $builder->tools());
    }

    public function test_footer_returns_footer_instance(): void
    {
        $builder = $this->createBuilder();

        $this->assertInstanceOf(Footer::class, $builder->footer());
    }

    public function test_form_returns_form_instance(): void
    {
        $form = Mockery::mock(Form::class);
        $form->shouldReceive('rows')->andReturn([]);
        $form->shouldReceive('getTab')->andReturn(
            Mockery::mock()->shouldReceive('isEmpty')->andReturn(true)->getMock()
        );
        $form->shouldReceive('resource')->andReturn('/admin/users');
        $form->shouldReceive('keyName')->andReturn('id');
        $form->shouldReceive('createdAtColumn')->andReturn('created_at');
        $form->shouldReceive('updatedAtColumn')->andReturn('updated_at');

        $builder = new Builder($form);

        $this->assertSame($form, $builder->form());
    }

    public function test_confirm_sets_values_and_returns_this(): void
    {
        $builder = $this->createBuilder();

        $result = $builder->confirm('Are you sure?', 'This action cannot be undone.');
        $this->assertSame($builder, $result);

        $this->assertSame('Are you sure?', $builder->confirm['title']);
        $this->assertSame('This action cannot be undone.', $builder->confirm['content']);
    }

    public function test_disable_header(): void
    {
        $builder = $this->createBuilder();

        $builder->disableHeader();

        $ref = new \ReflectionProperty($builder, 'showHeader');
        $ref->setAccessible(true);
        $this->assertFalse($ref->getValue($builder));
    }

    public function test_disable_footer(): void
    {
        $builder = $this->createBuilder();

        $builder->disableFooter();

        $ref = new \ReflectionProperty($builder, 'showFooter');
        $ref->setAccessible(true);
        $this->assertFalse($ref->getValue($builder));
    }

    public function test_get_element_id_generates_random(): void
    {
        $builder = $this->createBuilder();

        $elementId = $builder->getElementId();
        $this->assertStringStartsWith('form-', $elementId);
        $this->assertSame(13, strlen($elementId)); // 'form-' (5) + 8 random chars
    }

    public function test_set_element_id(): void
    {
        $builder = $this->createBuilder();

        $builder->setElementId('my-custom-id');
        $this->assertSame('my-custom-id', $builder->getElementId());
    }

    public function test_has_wrapper_false_by_default(): void
    {
        $builder = $this->createBuilder();

        $this->assertFalse($builder->hasWrapper());
    }

    public function test_wrap_sets_wrapper_and_returns_this(): void
    {
        $builder = $this->createBuilder();

        $closure = function ($view) {
            return $view;
        };

        $result = $builder->wrap($closure);
        $this->assertSame($builder, $result);
        $this->assertTrue($builder->hasWrapper());
    }

    public function test_hidden_fields_default_empty(): void
    {
        $builder = $this->createBuilder();

        $this->assertSame([], $builder->hiddenFields());
    }

    public function test_add_hidden_field(): void
    {
        $builder = $this->createBuilder();

        $field = Mockery::mock(Field::class);
        $builder->addHiddenField($field);

        $hiddenFields = $builder->hiddenFields();
        $this->assertCount(1, $hiddenFields);
        $this->assertSame($field, $hiddenFields[0]);
    }

    public function test_options_getter_returns_empty_by_default(): void
    {
        $builder = $this->createBuilder();

        $this->assertSame([], $builder->options());
    }

    public function test_options_setter_merges(): void
    {
        $builder = $this->createBuilder();

        $builder->options(['key1' => 'value1']);
        $builder->options(['key2' => 'value2']);

        $options = $builder->options();
        $this->assertSame(['key1' => 'value1', 'key2' => 'value2'], $options);
    }

    public function test_option_getter_and_setter(): void
    {
        $builder = $this->createBuilder();

        $this->assertNull($builder->option('foo'));

        $builder->option('foo', 'bar');
        $this->assertSame('bar', $builder->option('foo'));
    }

    public function test_close_returns_closing_tag(): void
    {
        $builder = $this->createBuilder();

        $result = $builder->close();
        $this->assertSame('</form>', $result);
    }

    public function test_has_file_false_by_default(): void
    {
        $builder = $this->createBuilder();

        $this->assertFalse($builder->hasFile());
    }
}
