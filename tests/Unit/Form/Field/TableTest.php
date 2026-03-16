<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\ArrayField;
use Dcat\Admin\Form\Field\HasMany;
use Dcat\Admin\Form\Field\Table;
use Dcat\Admin\Tests\TestCase;

class TableTest extends TestCase
{
    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    // -------------------------------------------------------
    // viewMode
    // -------------------------------------------------------

    public function test_view_mode_is_table(): void
    {
        $field = new Table('details', [function ($form) {
            // empty builder
        }]);

        $viewMode = $this->getProtectedProperty($field, 'viewMode');

        $this->assertSame('table', $viewMode);
    }

    // -------------------------------------------------------
    // class hierarchy
    // -------------------------------------------------------

    public function test_extends_array_field(): void
    {
        $field = new Table('details', [function ($form) {
            // empty builder
        }]);

        $this->assertInstanceOf(ArrayField::class, $field);
    }

    public function test_extends_has_many(): void
    {
        $field = new Table('details', [function ($form) {
            // empty builder
        }]);

        $this->assertInstanceOf(HasMany::class, $field);
    }

    // -------------------------------------------------------
    // constructor with label
    // -------------------------------------------------------

    public function test_constructor_with_label_and_builder(): void
    {
        $builder = function ($form) {};
        $field = new Table('details', ['Details Table', $builder]);

        $label = $this->getProtectedProperty($field, 'label');

        $this->assertSame('Details Table', $label);
    }

    public function test_constructor_with_builder_only(): void
    {
        $builder = function ($form) {};
        $field = new Table('details', [$builder]);

        $column = $this->getProtectedProperty($field, 'column');

        $this->assertSame('details', $column);
    }
}
