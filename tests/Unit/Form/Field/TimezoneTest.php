<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use DateTimeZone;
use Dcat\Admin\Form\Field\Select;
use Dcat\Admin\Form\Field\Timezone;
use Dcat\Admin\Tests\TestCase;

class TimezoneTest extends TestCase
{
    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    protected function createTimezone(string $column = 'timezone', string $label = 'Timezone'): Timezone
    {
        return new Timezone($column, [$label]);
    }

    public function test_is_instance_of_select(): void
    {
        $field = $this->createTimezone();

        $this->assertInstanceOf(Select::class, $field);
    }

    public function test_view_is_select(): void
    {
        $field = $this->createTimezone();

        $view = $this->getProtectedProperty($field, 'view');

        $this->assertSame('admin::form.select', $view);
    }

    public function test_timezone_identifiers_are_available(): void
    {
        $identifiers = DateTimeZone::listIdentifiers(DateTimeZone::ALL);

        $this->assertNotEmpty($identifiers);
        $this->assertContains('UTC', $identifiers);
        $this->assertContains('America/New_York', $identifiers);
        $this->assertContains('Asia/Shanghai', $identifiers);
    }

    public function test_column_is_set(): void
    {
        $field = $this->createTimezone('user_timezone');

        $column = $this->getProtectedProperty($field, 'column');

        $this->assertSame('user_timezone', $column);
    }
}
