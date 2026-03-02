<?php

namespace Dcat\Admin\Tests\Unit;

use Dcat\Admin\Show;
use Dcat\Admin\Tests\TestCase;
use Mockery;
use ReflectionClass;
use ReflectionProperty;

class ShowTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(Show::class));
    }

    public function test_implements_renderable(): void
    {
        $ref = new ReflectionClass(Show::class);
        $this->assertTrue($ref->implementsInterface(\Illuminate\Contracts\Support\Renderable::class));
    }

    public function test_uses_macroable_trait(): void
    {
        $ref = new ReflectionClass(Show::class);
        $traits = $this->getAllTraits($ref);
        $this->assertContains(\Illuminate\Support\Traits\Macroable::class, $traits);
    }

    public function test_uses_has_builder_events_trait(): void
    {
        $ref = new ReflectionClass(Show::class);
        $traits = $this->getAllTraits($ref);
        $this->assertContains(\Dcat\Admin\Traits\HasBuilderEvents::class, $traits);
    }

    public function test_view_default_value(): void
    {
        $prop = new ReflectionProperty(Show::class, 'view');
        $this->assertSame('admin::show.container', $prop->getDefaultValue());
    }

    public function test_key_name_default_value(): void
    {
        $prop = new ReflectionProperty(Show::class, 'keyName');
        $this->assertSame('id', $prop->getDefaultValue());
    }

    public function test_method_exists_resource(): void
    {
        $this->assertTrue(method_exists(Show::class, 'resource'));
    }

    public function test_method_exists_field(): void
    {
        $this->assertTrue(method_exists(Show::class, 'field'));
    }

    public function test_method_exists_relation(): void
    {
        $this->assertTrue(method_exists(Show::class, 'relation'));
    }

    public function test_method_exists_html(): void
    {
        $this->assertTrue(method_exists(Show::class, 'html'));
    }

    public function test_method_exists_divider(): void
    {
        $this->assertTrue(method_exists(Show::class, 'divider'));
    }

    public function test_method_exists_newline(): void
    {
        $this->assertTrue(method_exists(Show::class, 'newline'));
    }

    public function test_method_exists_panel(): void
    {
        $this->assertTrue(method_exists(Show::class, 'panel'));
    }

    public function test_method_exists_set_resource(): void
    {
        $this->assertTrue(method_exists(Show::class, 'setResource'));
    }

    public function test_method_exists_render(): void
    {
        $this->assertTrue(method_exists(Show::class, 'render'));
    }

    public function test_method_exists_row(): void
    {
        $this->assertTrue(method_exists(Show::class, 'row'));
    }

    public function test_method_exists_rows(): void
    {
        $this->assertTrue(method_exists(Show::class, 'rows'));
    }

    public function test_builder_property_default_null(): void
    {
        $prop = new ReflectionProperty(Show::class, 'builder');
        $this->assertNull($prop->getDefaultValue());
    }

    public function test_repository_property_default_null(): void
    {
        $prop = new ReflectionProperty(Show::class, 'repository');
        $this->assertNull($prop->getDefaultValue());
    }

    public function test_model_property_default_null(): void
    {
        $prop = new ReflectionProperty(Show::class, 'model');
        $this->assertNull($prop->getDefaultValue());
    }

    public function test_fields_property_exists(): void
    {
        $ref = new ReflectionClass(Show::class);
        $this->assertTrue($ref->hasProperty('fields'));
    }

    public function test_relations_property_exists(): void
    {
        $ref = new ReflectionClass(Show::class);
        $this->assertTrue($ref->hasProperty('relations'));
    }

    public function test_panel_property_exists(): void
    {
        $ref = new ReflectionClass(Show::class);
        $this->assertTrue($ref->hasProperty('panel'));
    }

    public function test_is_not_abstract(): void
    {
        $ref = new ReflectionClass(Show::class);
        $this->assertFalse($ref->isAbstract());
    }

    /**
     * Recursively collect all trait names used by a ReflectionClass.
     */
    private function getAllTraits(ReflectionClass $ref): array
    {
        $traits = [];
        foreach ($ref->getTraits() as $trait) {
            $traits[] = $trait->getName();
            $traits = array_merge($traits, $this->getAllTraits($trait));
        }

        return $traits;
    }
}
