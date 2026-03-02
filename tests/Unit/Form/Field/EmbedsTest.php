<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Contracts\FieldsCollection;
use Dcat\Admin\Form\Field;
use Dcat\Admin\Form\Field\Embeds;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class EmbedsTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    // -------------------------------------------------------
    // Class structure
    // -------------------------------------------------------

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(Embeds::class));
    }

    public function test_is_subclass_of_field(): void
    {
        $this->assertTrue(is_subclass_of(Embeds::class, Field::class));
    }

    public function test_implements_fields_collection(): void
    {
        $ref = new \ReflectionClass(Embeds::class);

        $this->assertTrue($ref->implementsInterface(FieldsCollection::class));
    }

    // -------------------------------------------------------
    // Default property values via reflection
    // -------------------------------------------------------

    public function test_builder_default_null(): void
    {
        $ref = new \ReflectionProperty(Embeds::class, 'builder');
        $ref->setAccessible(true);

        $this->assertNull($ref->getDefaultValue());
    }

    // -------------------------------------------------------
    // Method existence
    // -------------------------------------------------------

    public function test_method_get_validator_exists(): void
    {
        $this->assertTrue(method_exists(Embeds::class, 'getValidator'));
    }

    public function test_method_reset_input_key_exists(): void
    {
        $this->assertTrue(method_exists(Embeds::class, 'resetInputKey'));
    }

    public function test_method_field_exists(): void
    {
        $this->assertTrue(method_exists(Embeds::class, 'field'));
    }

    public function test_method_fields_exists(): void
    {
        $this->assertTrue(method_exists(Embeds::class, 'fields'));
    }

    public function test_method_render_exists(): void
    {
        $this->assertTrue(method_exists(Embeds::class, 'render'));
    }
}
