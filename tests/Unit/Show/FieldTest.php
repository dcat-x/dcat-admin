<?php

namespace Dcat\Admin\Tests\Unit\Show;

use Dcat\Admin\Show;
use Dcat\Admin\Show\Field;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;

class FieldTest extends TestCase
{
    /**
     * Get a protected/private property value via reflection.
     */
    protected function getProperty(Field $field, string $property): mixed
    {
        $ref = new \ReflectionProperty(Field::class, $property);
        $ref->setAccessible(true);

        return $ref->getValue($field);
    }

    /**
     * Call the protected defaultVariables() method via reflection.
     */
    protected function getDefaultVariables(Field $field): array
    {
        $ref = new \ReflectionMethod(Field::class, 'defaultVariables');
        $ref->setAccessible(true);

        return $ref->invoke($field);
    }

    public function test_constructor_sets_name(): void
    {
        $field = new Field('username', 'User Name');

        $this->assertSame('username', $field->getName());
    }

    public function test_constructor_sets_label(): void
    {
        $field = new Field('email', 'Email Address');

        $this->assertSame('Email Address', $field->getLabel());
    }

    public function test_constructor_auto_formats_label_from_name(): void
    {
        // When label is empty, formatLabel uses admin_trans_field then replaces underscores
        $field = new Field('user_name', '');

        $label = $field->getLabel();
        // The label should not contain underscores (they get replaced with spaces)
        $this->assertStringNotContainsString('_', $label);
    }

    public function test_get_name_returns_column_name(): void
    {
        $field = new Field('status', 'Status');

        $this->assertSame('status', $field->getName());
    }

    public function test_value_getter_returns_null_by_default(): void
    {
        $field = new Field('title', 'Title');

        $this->assertNull($field->value());
    }

    public function test_value_setter_stores_value(): void
    {
        $field = new Field('title', 'Title');
        $result = $field->value('Hello World');

        $this->assertSame($field, $result);
        $this->assertSame('Hello World', $field->value());
    }

    public function test_value_setter_with_various_types(): void
    {
        $field = new Field('data', 'Data');

        $field->value(42);
        $this->assertSame(42, $field->value());

        $field->value(['a', 'b']);
        $this->assertSame(['a', 'b'], $field->value());

        $field->value(true);
        $this->assertTrue($field->value());
    }

    public function test_as_callback_pushes_to_show_as_collection(): void
    {
        $field = new Field('title', 'Title');

        $callback = function ($value) {
            return strtoupper($value);
        };

        $result = $field->as($callback);

        $this->assertSame($field, $result);

        $showAs = $this->getProperty($field, 'showAs');
        $this->assertInstanceOf(Collection::class, $showAs);
        $this->assertCount(1, $showAs);
    }

    public function test_as_callback_chains_multiple(): void
    {
        $field = new Field('title', 'Title');

        $field->as(function ($v) {
            return $v;
        })->as(function ($v) {
            return $v;
        });

        $showAs = $this->getProperty($field, 'showAs');
        $this->assertCount(2, $showAs);
    }

    public function test_using_maps_value(): void
    {
        $field = new Field('status', 'Status');
        $field->value(1);

        $map = [0 => 'Inactive', 1 => 'Active', 2 => 'Banned'];
        $field->using($map);

        $showAs = $this->getProperty($field, 'showAs');
        $this->assertCount(1, $showAs);

        // Extract and call the callback to verify mapping logic
        [$callable, $params] = $showAs->first();
        $result = $callable->call(new Fluent(['status' => 1]), 1);

        $this->assertSame('Active', $result);
    }

    public function test_using_returns_default_for_missing_key(): void
    {
        $field = new Field('status', 'Status');
        $field->value(99);

        $map = [0 => 'Inactive', 1 => 'Active'];
        $field->using($map, 'Unknown');

        $showAs = $this->getProperty($field, 'showAs');

        [$callable, $params] = $showAs->first();
        $result = $callable->call(new Fluent, 99);

        $this->assertSame('Unknown', $result);
    }

    public function test_using_returns_default_for_null_value(): void
    {
        $field = new Field('status', 'Status');
        $field->value(null);

        $map = [0 => 'Inactive', 1 => 'Active'];
        $field->using($map, 'N/A');

        $showAs = $this->getProperty($field, 'showAs');

        [$callable, $params] = $showAs->first();
        $result = $callable->call(new Fluent, null);

        $this->assertSame('N/A', $result);
    }

    public function test_width_sets_field_and_label_widths(): void
    {
        $field = new Field('title', 'Title');
        $result = $field->width(10, 4);

        $this->assertSame($field, $result);

        $width = $this->getProperty($field, 'width');
        $this->assertSame(['label' => 4, 'field' => 10], $width);
    }

    public function test_width_default_label_width_is_two(): void
    {
        $field = new Field('title', 'Title');
        $field->width(6);

        $width = $this->getProperty($field, 'width');
        $this->assertSame(['label' => 2, 'field' => 6], $width);
    }

    public function test_default_width_values(): void
    {
        $field = new Field('title', 'Title');

        $width = $this->getProperty($field, 'width');
        $this->assertSame(['field' => 8, 'label' => 2], $width);
    }

    public function test_escape_is_true_by_default(): void
    {
        $field = new Field('title', 'Title');

        $escape = $this->getProperty($field, 'escape');
        $this->assertTrue($escape);
    }

    public function test_escape_can_be_set_to_false(): void
    {
        $field = new Field('title', 'Title');
        $result = $field->escape(false);

        $this->assertSame($field, $result);

        $escape = $this->getProperty($field, 'escape');
        $this->assertFalse($escape);
    }

    public function test_unescape_sets_escape_to_false(): void
    {
        $field = new Field('title', 'Title');
        $result = $field->unescape();

        $this->assertSame($field, $result);

        $escape = $this->getProperty($field, 'escape');
        $this->assertFalse($escape);
    }

    public function test_border_is_true_by_default(): void
    {
        $field = new Field('title', 'Title');

        $border = $this->getProperty($field, 'border');
        $this->assertTrue($border);
    }

    public function test_wrap_false_disables_border(): void
    {
        $field = new Field('title', 'Title');
        $result = $field->wrap(false);

        $this->assertSame($field, $result);

        $border = $this->getProperty($field, 'border');
        $this->assertFalse($border);
    }

    public function test_wrap_true_enables_border(): void
    {
        $field = new Field('title', 'Title');
        $field->wrap(false);
        $field->wrap(true);

        $border = $this->getProperty($field, 'border');
        $this->assertTrue($border);
    }

    public function test_default_variables_contains_all_keys(): void
    {
        $field = new Field('title', 'Title');
        $field->value('Some content');

        $variables = $this->getDefaultVariables($field);

        $this->assertArrayHasKey('content', $variables);
        $this->assertArrayHasKey('escape', $variables);
        $this->assertArrayHasKey('label', $variables);
        $this->assertArrayHasKey('wrapped', $variables);
        $this->assertArrayHasKey('width', $variables);

        $this->assertSame('Some content', $variables['content']);
        $this->assertSame('Title', $variables['label']);
    }

    public function test_default_variables_content_reflects_value(): void
    {
        $field = new Field('title', 'Title');
        $field->value('Test Content');

        $variables = $this->getDefaultVariables($field);

        $this->assertSame('Test Content', $variables['content']);
        $this->assertTrue($variables['escape']);
        $this->assertTrue($variables['wrapped']);
    }

    public function test_set_parent_returns_self(): void
    {
        $field = new Field('title', 'Title');
        $show = new Show(['title' => 'Test']);

        $result = $field->setParent($show);

        $this->assertSame($field, $result);
    }

    public function test_fill_extracts_value_from_model(): void
    {
        $field = new Field('name', 'Name');

        $model = new Fluent(['name' => 'John', 'email' => 'john@example.com']);
        $field->fill($model);

        $this->assertSame('John', $field->value());
    }

    public function test_fill_with_missing_key_sets_null(): void
    {
        $field = new Field('missing', 'Missing');

        $model = new Fluent(['name' => 'John']);
        $field->fill($model);

        $this->assertNull($field->value());
    }

    public function test_when_executes_callback_if_truthy(): void
    {
        $field = new Field('title', 'Title');
        $called = false;

        $result = $field->when(true, function ($f) use (&$called) {
            $called = true;
        });

        $this->assertTrue($called);
        $this->assertSame($field, $result);
    }

    public function test_when_skips_callback_if_falsy(): void
    {
        $field = new Field('title', 'Title');
        $called = false;

        $result = $field->when(false, function ($f) use (&$called) {
            $called = true;
        });

        $this->assertFalse($called);
        $this->assertSame($field, $result);
    }

    public function test_when_passes_value_to_callback(): void
    {
        $field = new Field('title', 'Title');
        $receivedValue = null;

        $field->when('some_value', function ($f, $value) use (&$receivedValue) {
            $receivedValue = $value;
        });

        $this->assertSame('some_value', $receivedValue);
    }

    public function test_extend_registers_custom_field(): void
    {
        Field::extend('customField', \Closure::class);

        $extensions = Field::extensions();
        $this->assertArrayHasKey('customField', $extensions);

        // Clean up static state
        $ref = new \ReflectionProperty(Field::class, 'extendedFields');
        $ref->setAccessible(true);
        $ref->setValue(null, []);
    }

    public function test_extensions_returns_all_registered(): void
    {
        $ref = new \ReflectionProperty(Field::class, 'extendedFields');
        $ref->setAccessible(true);
        $original = $ref->getValue(null);

        Field::extend('fieldA', 'ClassA');
        Field::extend('fieldB', 'ClassB');

        $extensions = Field::extensions();
        $this->assertArrayHasKey('fieldA', $extensions);
        $this->assertArrayHasKey('fieldB', $extensions);

        // Restore
        $ref->setValue(null, $original);
    }

    public function test_explode_registers_callback(): void
    {
        $field = new Field('tags', 'Tags');
        $result = $field->explode(',');

        $this->assertSame($field, $result);

        $showAs = $this->getProperty($field, 'showAs');
        $this->assertCount(1, $showAs);

        // Verify the explode callback works
        [$callable, $params] = $showAs->first();
        $exploded = $callable->call(new Fluent, 'a,b,c');
        $this->assertSame(['a', 'b', 'c'], $exploded);
    }

    public function test_explode_returns_empty_array_for_empty_value(): void
    {
        $field = new Field('tags', 'Tags');
        $field->explode(',');

        $showAs = $this->getProperty($field, 'showAs');

        [$callable, $params] = $showAs->first();
        $result = $callable->call(new Fluent, '');
        $this->assertSame([], $result);
    }

    public function test_explode_returns_array_as_is(): void
    {
        $field = new Field('tags', 'Tags');
        $field->explode(',');

        $showAs = $this->getProperty($field, 'showAs');

        [$callable, $params] = $showAs->first();
        $input = ['already', 'array'];
        $result = $callable->call(new Fluent, $input);
        $this->assertSame($input, $result);
    }

    public function test_explode_with_custom_delimiter(): void
    {
        $field = new Field('tags', 'Tags');
        $field->explode('|');

        $showAs = $this->getProperty($field, 'showAs');

        [$callable, $params] = $showAs->first();
        $result = $callable->call(new Fluent, 'x|y|z');
        $this->assertSame(['x', 'y', 'z'], $result);
    }
}
