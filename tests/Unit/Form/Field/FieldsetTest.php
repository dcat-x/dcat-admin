<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field;
use Dcat\Admin\Form\Field\Fieldset;
use Dcat\Admin\Tests\TestCase;

class FieldsetTest extends TestCase
{
    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    public function test_name_starts_with_fieldset_prefix(): void
    {
        $fieldset = new Fieldset;

        $name = $this->getProtectedProperty($fieldset, 'name');

        $this->assertStringStartsWith('fieldset-', $name);
    }

    public function test_end_returns_closing_divs(): void
    {
        $fieldset = new Fieldset;

        $result = $fieldset->end();

        $this->assertSame('</div></div>', $result);
    }

    public function test_start_returns_html_containing_title(): void
    {
        $fieldset = new Fieldset;

        $result = $fieldset->start('My Section');

        $this->assertStringContainsString('My Section', $result);
    }

    public function test_start_returns_html_containing_name(): void
    {
        $fieldset = new Fieldset;

        $name = $this->getProtectedProperty($fieldset, 'name');
        $result = $fieldset->start('Title');

        $this->assertStringContainsString($name, $result);
    }

    public function test_collapsed_returns_self_for_fluent_interface(): void
    {
        $fieldset = new Fieldset;

        $result = $fieldset->collapsed();

        $this->assertSame($fieldset, $result);
    }

    public function test_is_not_instance_of_field(): void
    {
        $fieldset = new Fieldset;

        $this->assertNotInstanceOf(Field::class, $fieldset);
    }
}
