<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\Tags;
use Dcat\Admin\Tests\TestCase;

class TagsTest extends TestCase
{
    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    protected function createTags(string $column = 'tags', string $label = 'Tags'): Tags
    {
        return new Tags($column, [$label]);
    }

    // -------------------------------------------------------
    // pluck()
    // -------------------------------------------------------

    public function test_pluck_sets_visible_column_and_key(): void
    {
        $field = $this->createTags();

        $result = $field->pluck('name', 'id');

        $this->assertSame($field, $result);
        $this->assertSame('name', $this->getProtectedProperty($field, 'visibleColumn'));
        $this->assertSame('id', $this->getProtectedProperty($field, 'key'));
        $this->assertTrue($this->getProtectedProperty($field, 'keyAsValue'));
    }

    public function test_pluck_defaults_key_to_id(): void
    {
        $field = $this->createTags();

        $field->pluck('title');

        $this->assertSame('title', $this->getProtectedProperty($field, 'visibleColumn'));
        $this->assertSame('id', $this->getProtectedProperty($field, 'key'));
    }

    public function test_pluck_with_custom_key(): void
    {
        $field = $this->createTags();

        $field->pluck('label', 'slug');

        $this->assertSame('label', $this->getProtectedProperty($field, 'visibleColumn'));
        $this->assertSame('slug', $this->getProtectedProperty($field, 'key'));
    }

    // -------------------------------------------------------
    // options()
    // -------------------------------------------------------

    public function test_options_with_closure(): void
    {
        $field = $this->createTags();
        $closure = function () {
            return ['tag1', 'tag2'];
        };

        $result = $field->options($closure);

        $this->assertSame($field, $result);
        $this->assertInstanceOf(\Closure::class, $this->getProtectedProperty($field, 'options'));
    }

    public function test_options_with_array_no_pluck(): void
    {
        $field = $this->createTags();

        $result = $field->options(['PHP', 'Laravel', 'Vue']);

        $this->assertSame($field, $result);
    }

    public function test_options_with_array_and_pluck(): void
    {
        $field = $this->createTags();
        $field->pluck('name', 'id');

        $field->options([1 => 'PHP', 2 => 'Laravel']);

        $options = $this->getProtectedProperty($field, 'options');
        $this->assertSame('PHP', $options[1] ?? null);
        $this->assertSame('Laravel', $options[2] ?? null);
    }

    // -------------------------------------------------------
    // prepareInputValue()
    // -------------------------------------------------------

    public function test_prepare_input_value_filters_empty_strings(): void
    {
        $field = $this->createTags();

        $method = new \ReflectionMethod($field, 'prepareInputValue');
        $method->setAccessible(true);

        $result = $method->invoke($field, ['tag1', '', 'tag2', '']);
        $this->assertNotContains('', $result);
        $this->assertContains('tag1', $result);
        $this->assertContains('tag2', $result);
    }

    public function test_prepare_input_value_passes_non_array_through(): void
    {
        $field = $this->createTags();

        $method = new \ReflectionMethod($field, 'prepareInputValue');
        $method->setAccessible(true);

        $this->assertSame('single_tag', $method->invoke($field, 'single_tag'));
    }

    // -------------------------------------------------------
    // value()
    // -------------------------------------------------------

    public function test_value_getter_returns_array(): void
    {
        $field = $this->createTags();

        $result = $field->value();

        $this->assertIsArray($result);
    }

    public function test_value_setter_converts_to_array(): void
    {
        $field = $this->createTags();

        $result = $field->value(['tag1', 'tag2']);

        $this->assertSame($field, $result);
        $value = $this->getProtectedProperty($field, 'value');
        $this->assertIsArray($value);
        $this->assertContains('tag1', $value);
    }

    // -------------------------------------------------------
    // default value
    // -------------------------------------------------------

    public function test_default_value_is_empty_array(): void
    {
        $field = $this->createTags();

        $value = $this->getProtectedProperty($field, 'value');

        $this->assertSame([], $value);
    }

    public function test_key_as_value_default_false(): void
    {
        $field = $this->createTags();

        $this->assertFalse($this->getProtectedProperty($field, 'keyAsValue'));
    }
}
