<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\Listbox;
use Dcat\Admin\Form\Field\MultipleSelect;
use Dcat\Admin\Form\Field\Select;
use Dcat\Admin\Tests\TestCase;

class ListboxTest extends TestCase
{
    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    // -------------------------------------------------------
    // instanceof & inheritance
    // -------------------------------------------------------

    public function test_is_instance_of_multiple_select(): void
    {
        $field = new Listbox('roles', ['Roles']);

        $this->assertInstanceOf(MultipleSelect::class, $field);
    }

    public function test_is_instance_of_select(): void
    {
        $field = new Listbox('roles', ['Roles']);

        $this->assertInstanceOf(Select::class, $field);
    }

    // -------------------------------------------------------
    // settings()
    // -------------------------------------------------------

    public function test_settings_stores_settings(): void
    {
        $field = new Listbox('permissions', ['Permissions']);

        $result = $field->settings(['showFilterInputs' => false]);

        $this->assertSame($field, $result);

        $settings = $this->getProtectedProperty($field, 'settings');
        $this->assertSame(['showFilterInputs' => false], $settings);
    }

    public function test_settings_replaces_previous_settings(): void
    {
        $field = new Listbox('permissions', ['Permissions']);

        $field->settings(['key1' => 'value1']);
        $field->settings(['key2' => 'value2']);

        $settings = $this->getProtectedProperty($field, 'settings');
        $this->assertSame(['key2' => 'value2'], $settings);
        $this->assertArrayNotHasKey('key1', $settings);
    }

    public function test_default_settings_is_empty_array(): void
    {
        $field = new Listbox('roles', ['Roles']);

        $settings = $this->getProtectedProperty($field, 'settings');

        $this->assertSame([], $settings);
    }

    // -------------------------------------------------------
    // construction
    // -------------------------------------------------------

    public function test_constructor_sets_column(): void
    {
        $field = new Listbox('permissions', ['Permissions']);

        $column = $this->getProtectedProperty($field, 'column');

        $this->assertSame('permissions', $column);
    }

    public function test_constructor_sets_label(): void
    {
        $field = new Listbox('roles', ['User Roles']);

        $label = $this->getProtectedProperty($field, 'label');

        $this->assertSame('User Roles', $label);
    }
}
