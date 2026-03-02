<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\CanCascadeFields;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class CanCascadeFieldsTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    // -------------------------------------------------------
    // Trait existence
    // -------------------------------------------------------

    public function test_trait_exists(): void
    {
        $this->assertTrue(trait_exists(CanCascadeFields::class));
    }

    // -------------------------------------------------------
    // Method existence
    // -------------------------------------------------------

    public function test_when_method_exists(): void
    {
        $this->assertTrue(method_exists(CanCascadeFields::class, 'when'));
    }

    public function test_get_default_operator_method_exists(): void
    {
        $this->assertTrue(method_exists(CanCascadeFields::class, 'getDefaultOperator'));
    }

    public function test_format_values_method_exists(): void
    {
        $this->assertTrue(method_exists(CanCascadeFields::class, 'formatValues'));
    }

    public function test_get_cascade_class_method_exists(): void
    {
        $this->assertTrue(method_exists(CanCascadeFields::class, 'getCascadeClass'));
    }

    public function test_add_cascade_script_method_exists(): void
    {
        $this->assertTrue(method_exists(CanCascadeFields::class, 'addCascadeScript'));
    }

    public function test_get_cascade_script_method_exists(): void
    {
        $this->assertTrue(method_exists(CanCascadeFields::class, 'getCascadeScript'));
    }

    public function test_get_form_front_value_method_exists(): void
    {
        $this->assertTrue(method_exists(CanCascadeFields::class, 'getFormFrontValue'));
    }

    // -------------------------------------------------------
    // Default property values via reflection on the trait
    // -------------------------------------------------------

    public function test_conditions_default_is_empty_array(): void
    {
        $reflection = new \ReflectionClass(CanCascadeFields::class);
        $properties = $reflection->getDefaultProperties();

        $this->assertArrayHasKey('conditions', $properties);
        $this->assertSame([], $properties['conditions']);
    }

    public function test_cascade_groups_default_is_empty_array(): void
    {
        $reflection = new \ReflectionClass(CanCascadeFields::class);
        $properties = $reflection->getDefaultProperties();

        $this->assertArrayHasKey('cascadeGroups', $properties);
        $this->assertSame([], $properties['cascadeGroups']);
    }

    // -------------------------------------------------------
    // Method visibility checks
    // -------------------------------------------------------

    public function test_when_is_public(): void
    {
        $method = new \ReflectionMethod(CanCascadeFields::class, 'when');
        $this->assertTrue($method->isPublic());
    }

    public function test_get_default_operator_is_protected(): void
    {
        $method = new \ReflectionMethod(CanCascadeFields::class, 'getDefaultOperator');
        $this->assertTrue($method->isProtected());
    }

    public function test_format_values_is_protected(): void
    {
        $method = new \ReflectionMethod(CanCascadeFields::class, 'formatValues');
        $this->assertTrue($method->isProtected());
    }

    public function test_get_cascade_class_is_protected(): void
    {
        $method = new \ReflectionMethod(CanCascadeFields::class, 'getCascadeClass');
        $this->assertTrue($method->isProtected());
    }

    public function test_add_cascade_script_is_protected(): void
    {
        $method = new \ReflectionMethod(CanCascadeFields::class, 'addCascadeScript');
        $this->assertTrue($method->isProtected());
    }

    public function test_get_cascade_script_is_protected(): void
    {
        $method = new \ReflectionMethod(CanCascadeFields::class, 'getCascadeScript');
        $this->assertTrue($method->isProtected());
    }

    public function test_get_form_front_value_is_protected(): void
    {
        $method = new \ReflectionMethod(CanCascadeFields::class, 'getFormFrontValue');
        $this->assertTrue($method->isProtected());
    }
}
