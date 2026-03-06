<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\Autocomplete;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class AutocompleteTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function createAutocomplete(string $column = 'name', string $label = 'Name'): Autocomplete
    {
        return new Autocomplete($column, [$label]);
    }

    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    protected function invokeProtectedMethod(object $object, string $method, array $arguments = [])
    {
        $reflection = new \ReflectionMethod($object, $method);
        $reflection->setAccessible(true);

        return $reflection->invokeArgs($object, $arguments);
    }

    public function test_view_default_is_admin_form_autocomplete(): void
    {
        $field = $this->createAutocomplete();

        $this->assertSame('admin::form.autocomplete', $this->getProtectedProperty($field, 'view'));
    }

    public function test_configs_default_includes_auto_select_first(): void
    {
        $field = $this->createAutocomplete();

        $configs = $this->getProtectedProperty($field, 'configs');

        $this->assertTrue($configs['autoSelectFirst'] ?? false);
    }

    public function test_group_by_default_is_group(): void
    {
        $field = $this->createAutocomplete();

        $this->assertSame('__group__', $this->getProtectedProperty($field, 'groupBy'));
    }

    public function test_groups_property_default_is_empty_array(): void
    {
        $field = $this->createAutocomplete();

        $this->assertSame([], $this->getProtectedProperty($field, 'groups'));
    }

    public function test_datalist_delegates_to_options(): void
    {
        $field = $this->createAutocomplete();

        $result = $field->datalist(['foo', 'bar']);

        $this->assertSame($field, $result);

        $options = $this->getProtectedProperty($field, 'options');
        $this->assertCount(2, $options);
        $this->assertSame('foo', $options[0]['value']);
        $this->assertSame('bar', $options[1]['value']);
    }

    public function test_options_formats_scalar_values_to_value_data_shape(): void
    {
        $field = $this->createAutocomplete();

        $field->options(['one', 'two']);

        $options = $this->getProtectedProperty($field, 'options');

        $this->assertSame(
            [
                ['value' => 'one', 'data' => []],
                ['value' => 'two', 'data' => []],
            ],
            $options
        );
    }

    public function test_groups_merges_input_and_returns_self(): void
    {
        $field = $this->createAutocomplete();
        $groups = [
            ['label' => 'Cities', 'options' => ['Shanghai', 'Beijing']],
        ];

        $result = $field->groups($groups);

        $this->assertSame($field, $result);
        $this->assertSame($groups, $this->getProtectedProperty($field, 'groups'));
    }

    public function test_configs_merges_values_and_returns_self(): void
    {
        $field = $this->createAutocomplete();

        $result = $field->configs(['minChars' => 2, 'autoSelectFirst' => false]);

        $this->assertSame($field, $result);

        $configs = $this->getProtectedProperty($field, 'configs');
        $this->assertSame(2, $configs['minChars']);
        $this->assertFalse($configs['autoSelectFirst']);
    }

    public function test_group_by_updates_internal_group_key(): void
    {
        $field = $this->createAutocomplete();

        $result = $field->groupBy('category');

        $this->assertSame($field, $result);
        $this->assertSame('category', $this->getProtectedProperty($field, 'groupBy'));
    }

    public function test_ajax_sets_ajax_variables(): void
    {
        $field = $this->createAutocomplete();

        $result = $field->ajax('/api/autocomplete', 'id', 'group');

        $this->assertSame($field, $result);

        $variables = $this->getProtectedProperty($field, 'variables');
        $ajax = $variables['ajax'] ?? [];
        $this->assertStringContainsString('/api/autocomplete', $ajax['url'] ?? '');
        $this->assertSame('id', $ajax['valueField'] ?? null);
        $this->assertSame('group', $ajax['groupField'] ?? null);
    }

    public function test_format_group_options_adds_group_data_and_clears_groups(): void
    {
        $field = $this->createAutocomplete();

        $field->groupBy('category');
        $field->groups([
            ['label' => 'Fruit', 'options' => ['Apple']],
        ]);

        $result = $this->invokeProtectedMethod($field, 'formatGroupOptions');

        $this->assertSame($field, $result);

        $options = $this->getProtectedProperty($field, 'options');
        $this->assertSame('Apple', $options[0]['value']);
        $this->assertSame('Fruit', $options[0]['data']['category']);
        $this->assertSame([], $this->getProtectedProperty($field, 'groups'));
    }

    public function test_format_options_filters_items_without_value_key(): void
    {
        $field = $this->createAutocomplete();

        $result = $this->invokeProtectedMethod($field, 'formatOptions', [[
            ['value' => 'A', 'data' => []],
            ['label' => 'Missing value'],
        ]]);

        $this->assertCount(1, $result);
        $this->assertSame('A', array_values($result)[0]['value']);
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
}
