<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\Autocomplete;
use Dcat\Admin\Form\Field\Text;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class AutocompleteTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(Autocomplete::class));
    }

    public function test_is_subclass_of_text(): void
    {
        $this->assertTrue(is_subclass_of(Autocomplete::class, Text::class));
    }

    public function test_view_default_is_admin_form_autocomplete(): void
    {
        $ref = new \ReflectionProperty(Autocomplete::class, 'view');
        $ref->setAccessible(true);

        $this->assertSame('admin::form.autocomplete', $ref->getDefaultValue());
    }

    public function test_configs_default_includes_auto_select_first(): void
    {
        $ref = new \ReflectionProperty(Autocomplete::class, 'configs');
        $ref->setAccessible(true);

        $defaults = $ref->getDefaultValue();

        $this->assertIsArray($defaults);
        $this->assertArrayHasKey('autoSelectFirst', $defaults);
        $this->assertTrue($defaults['autoSelectFirst']);
    }

    public function test_group_by_default_is_group(): void
    {
        $ref = new \ReflectionProperty(Autocomplete::class, 'groupBy');
        $ref->setAccessible(true);

        $this->assertSame('__group__', $ref->getDefaultValue());
    }

    public function test_method_datalist_exists(): void
    {
        $this->assertTrue(method_exists(Autocomplete::class, 'datalist'));
    }

    public function test_method_groups_exists(): void
    {
        $this->assertTrue(method_exists(Autocomplete::class, 'groups'));
    }

    public function test_method_options_exists(): void
    {
        $this->assertTrue(method_exists(Autocomplete::class, 'options'));
    }

    public function test_method_configs_exists(): void
    {
        $this->assertTrue(method_exists(Autocomplete::class, 'configs'));
    }

    public function test_method_group_by_exists(): void
    {
        $this->assertTrue(method_exists(Autocomplete::class, 'groupBy'));
    }

    public function test_method_ajax_exists(): void
    {
        $this->assertTrue(method_exists(Autocomplete::class, 'ajax'));
    }

    public function test_method_render_exists(): void
    {
        $this->assertTrue(method_exists(Autocomplete::class, 'render'));
    }

    public function test_method_format_group_options_exists(): void
    {
        $this->assertTrue(method_exists(Autocomplete::class, 'formatGroupOptions'));
    }

    public function test_method_format_options_exists(): void
    {
        $this->assertTrue(method_exists(Autocomplete::class, 'formatOptions'));
    }

    public function test_groups_property_default_is_empty_array(): void
    {
        $ref = new \ReflectionProperty(Autocomplete::class, 'groups');
        $ref->setAccessible(true);

        $this->assertSame([], $ref->getDefaultValue());
    }

    public function test_datalist_method_is_public(): void
    {
        $ref = new \ReflectionMethod(Autocomplete::class, 'datalist');

        $this->assertTrue($ref->isPublic());
    }

    public function test_groups_method_is_public(): void
    {
        $ref = new \ReflectionMethod(Autocomplete::class, 'groups');

        $this->assertTrue($ref->isPublic());
    }

    public function test_options_method_is_public(): void
    {
        $ref = new \ReflectionMethod(Autocomplete::class, 'options');

        $this->assertTrue($ref->isPublic());
    }

    public function test_configs_method_is_public(): void
    {
        $ref = new \ReflectionMethod(Autocomplete::class, 'configs');

        $this->assertTrue($ref->isPublic());
    }

    public function test_group_by_method_is_public(): void
    {
        $ref = new \ReflectionMethod(Autocomplete::class, 'groupBy');

        $this->assertTrue($ref->isPublic());
    }

    public function test_ajax_method_is_public(): void
    {
        $ref = new \ReflectionMethod(Autocomplete::class, 'ajax');

        $this->assertTrue($ref->isPublic());
    }

    public function test_format_group_options_is_protected(): void
    {
        $ref = new \ReflectionMethod(Autocomplete::class, 'formatGroupOptions');

        $this->assertTrue($ref->isProtected());
    }

    public function test_format_options_is_protected(): void
    {
        $ref = new \ReflectionMethod(Autocomplete::class, 'formatOptions');

        $this->assertTrue($ref->isProtected());
    }

    public function test_ajax_method_accepts_three_parameters(): void
    {
        $ref = new \ReflectionMethod(Autocomplete::class, 'ajax');
        $params = $ref->getParameters();

        $this->assertCount(3, $params);
        $this->assertSame('url', $params[0]->getName());
        $this->assertSame('valueField', $params[1]->getName());
        $this->assertSame('groupField', $params[2]->getName());
    }

    public function test_group_by_method_accepts_string_parameter(): void
    {
        $ref = new \ReflectionMethod(Autocomplete::class, 'groupBy');
        $params = $ref->getParameters();

        $this->assertCount(1, $params);
        $this->assertSame('groupBy', $params[0]->getName());
    }

    public function test_constructor_accepts_column_and_arguments(): void
    {
        $ref = new \ReflectionMethod(Autocomplete::class, '__construct');
        $params = $ref->getParameters();

        $this->assertCount(2, $params);
        $this->assertSame('column', $params[0]->getName());
        $this->assertSame('arguments', $params[1]->getName());
    }
}
