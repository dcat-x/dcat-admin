<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\Date;
use Dcat\Admin\Tests\TestCase;

class DateTest extends TestCase
{
    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    protected function createDate(string $column = 'created_at', string $label = 'Created At'): Date
    {
        return new Date($column, [$label]);
    }

    // -------------------------------------------------------
    // format()
    // -------------------------------------------------------

    public function test_format_default_value(): void
    {
        $field = $this->createDate();

        $format = $this->getProtectedProperty($field, 'format');

        $this->assertSame('YYYY-MM-DD', $format);
    }

    public function test_format_sets_value(): void
    {
        $field = $this->createDate();

        $result = $field->format('DD/MM/YYYY');

        $this->assertSame($field, $result);
        $format = $this->getProtectedProperty($field, 'format');
        $this->assertSame('DD/MM/YYYY', $format);
    }

    public function test_format_with_time(): void
    {
        $field = $this->createDate();

        $field->format('YYYY-MM-DD HH:mm:ss');

        $format = $this->getProtectedProperty($field, 'format');
        $this->assertSame('YYYY-MM-DD HH:mm:ss', $format);
    }

    // -------------------------------------------------------
    // prepareInputValue()
    // -------------------------------------------------------

    public function test_prepare_input_value_converts_empty_string_to_null(): void
    {
        $field = $this->createDate();

        $method = new \ReflectionMethod($field, 'prepareInputValue');
        $method->setAccessible(true);

        $this->assertNull($method->invoke($field, ''));
    }

    public function test_prepare_input_value_passes_non_empty_through(): void
    {
        $field = $this->createDate();

        $method = new \ReflectionMethod($field, 'prepareInputValue');
        $method->setAccessible(true);

        $this->assertSame('2024-01-15', $method->invoke($field, '2024-01-15'));
    }

    public function test_prepare_input_value_passes_null_through(): void
    {
        $field = $this->createDate();

        $method = new \ReflectionMethod($field, 'prepareInputValue');
        $method->setAccessible(true);

        $this->assertNull($method->invoke($field, null));
    }

    // -------------------------------------------------------
    // static assets
    // -------------------------------------------------------

    public function test_js_assets_defined(): void
    {
        $this->assertContains('@moment', Date::$js);
        $this->assertContains('@bootstrap-datetimepicker', Date::$js);
    }

    public function test_css_assets_defined(): void
    {
        $this->assertContains('@bootstrap-datetimepicker', Date::$css);
    }
}
