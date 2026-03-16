<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit;

use Dcat\Admin\Show;
use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Traits\HasBuilderEvents;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Traits\Macroable;
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

    public function test_implements_renderable(): void
    {
        $ref = new ReflectionClass(Show::class);
        $this->assertTrue($ref->implementsInterface(Renderable::class));
    }

    public function test_uses_macroable_trait(): void
    {
        $ref = new ReflectionClass(Show::class);
        $traits = $this->getAllTraits($ref);
        $this->assertContains(Macroable::class, $traits);
    }

    public function test_uses_has_builder_events_trait(): void
    {
        $ref = new ReflectionClass(Show::class);
        $traits = $this->getAllTraits($ref);
        $this->assertContains(HasBuilderEvents::class, $traits);
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

    public function test_resource_and_set_resource_work_as_expected(): void
    {
        $show = new Show;

        $show->setResource('users');

        $this->assertStringContainsString('/users', $show->resource());
    }

    public function test_field_relation_and_content_methods_add_items(): void
    {
        $show = new Show(['id' => 1, 'name' => 'Taylor']);

        $show->field('name', 'Name');
        $show->relation('roles', 'Roles', function () {});
        $show->html('<div>hello</div>');
        $show->divider();
        $show->newline();

        $this->assertCount(4, $show->fields());
        $this->assertCount(1, $show->relations());
        $this->assertNotNull($show->panel());
    }

    public function test_row_and_rows_collection_work(): void
    {
        $show = new Show(['id' => 1, 'name' => 'Taylor']);

        $show->row(function () {
            return 'row';
        });

        $this->assertCount(1, $show->rows());
    }

    public function test_render_outputs_show_container_markup(): void
    {
        $show = new Show(['id' => 1, 'name' => 'Taylor']);
        $show->field('name', 'Name');

        $html = $show->render();

        $this->assertIsString($html);
        $this->assertStringContainsString('box-body', $html);
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
