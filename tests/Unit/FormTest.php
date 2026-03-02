<?php

namespace Dcat\Admin\Tests\Unit;

use Dcat\Admin\Form;
use Dcat\Admin\Tests\TestCase;
use Mockery;
use ReflectionClass;
use ReflectionProperty;

class FormTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(Form::class));
    }

    public function test_implements_renderable(): void
    {
        $ref = new ReflectionClass(Form::class);
        $this->assertTrue($ref->implementsInterface(\Illuminate\Contracts\Support\Renderable::class));
    }

    public function test_uses_macroable_trait(): void
    {
        $ref = new ReflectionClass(Form::class);
        $traits = $this->getAllTraits($ref);
        $this->assertContains(\Illuminate\Support\Traits\Macroable::class, $traits);
    }

    public function test_uses_handle_cascade_fields_trait(): void
    {
        $ref = new ReflectionClass(Form::class);
        $traits = $this->getAllTraits($ref);
        $this->assertContains(\Dcat\Admin\Form\Concerns\HandleCascadeFields::class, $traits);
    }

    public function test_uses_has_data_permission_trait(): void
    {
        $ref = new ReflectionClass(Form::class);
        $traits = $this->getAllTraits($ref);
        $this->assertContains(\Dcat\Admin\Form\Concerns\HasDataPermission::class, $traits);
    }

    public function test_uses_has_events_trait(): void
    {
        $ref = new ReflectionClass(Form::class);
        $traits = $this->getAllTraits($ref);
        $this->assertContains(\Dcat\Admin\Form\Concerns\HasEvents::class, $traits);
    }

    public function test_uses_has_files_trait(): void
    {
        $ref = new ReflectionClass(Form::class);
        $traits = $this->getAllTraits($ref);
        $this->assertContains(\Dcat\Admin\Form\Concerns\HasFiles::class, $traits);
    }

    public function test_uses_has_rows_trait(): void
    {
        $ref = new ReflectionClass(Form::class);
        $traits = $this->getAllTraits($ref);
        $this->assertContains(\Dcat\Admin\Form\Concerns\HasRows::class, $traits);
    }

    public function test_uses_has_tabs_trait(): void
    {
        $ref = new ReflectionClass(Form::class);
        $traits = $this->getAllTraits($ref);
        $this->assertContains(\Dcat\Admin\Form\Concerns\HasTabs::class, $traits);
    }

    public function test_uses_has_builder_events_trait(): void
    {
        $ref = new ReflectionClass(Form::class);
        $traits = $this->getAllTraits($ref);
        $this->assertContains(\Dcat\Admin\Traits\HasBuilderEvents::class, $traits);
    }

    public function test_uses_has_form_response_trait(): void
    {
        $ref = new ReflectionClass(Form::class);
        $traits = $this->getAllTraits($ref);
        $this->assertContains(\Dcat\Admin\Traits\HasFormResponse::class, $traits);
    }

    public function test_uses_resolve_field_trait(): void
    {
        $ref = new ReflectionClass(Form::class);
        $traits = $this->getAllTraits($ref);
        $this->assertContains(\Dcat\Admin\Form\ResolveField::class, $traits);
    }

    public function test_has_remove_flag_name_constant(): void
    {
        $this->assertSame('_remove_', Form::REMOVE_FLAG_NAME);
    }

    public function test_has_current_url_name_constant(): void
    {
        $this->assertSame('_current_', Form::CURRENT_URL_NAME);
    }

    public function test_method_exists_resource(): void
    {
        $this->assertTrue(method_exists(Form::class, 'resource'));
    }

    public function test_method_exists_title(): void
    {
        $this->assertTrue(method_exists(Form::class, 'title'));
    }

    public function test_method_exists_builder(): void
    {
        $this->assertTrue(method_exists(Form::class, 'builder'));
    }

    public function test_method_exists_model(): void
    {
        $this->assertTrue(method_exists(Form::class, 'model'));
    }

    public function test_method_exists_action(): void
    {
        $this->assertTrue(method_exists(Form::class, 'action'));
    }

    public function test_method_exists_footer(): void
    {
        $this->assertTrue(method_exists(Form::class, 'footer'));
    }

    public function test_method_exists_disable_header(): void
    {
        $this->assertTrue(method_exists(Form::class, 'disableHeader'));
    }

    public function test_method_exists_disable_view_check(): void
    {
        $this->assertTrue(method_exists(Form::class, 'disableViewCheck'));
    }

    public function test_method_exists_disable_editing_check(): void
    {
        $this->assertTrue(method_exists(Form::class, 'disableEditingCheck'));
    }

    public function test_method_exists_disable_creating_check(): void
    {
        $this->assertTrue(method_exists(Form::class, 'disableCreatingCheck'));
    }

    public function test_method_exists_is_creating(): void
    {
        $this->assertTrue(method_exists(Form::class, 'isCreating'));
    }

    public function test_method_exists_is_editing(): void
    {
        $this->assertTrue(method_exists(Form::class, 'isEditing'));
    }

    public function test_method_exists_is_deleting(): void
    {
        $this->assertTrue(method_exists(Form::class, 'isDeleting'));
    }

    public function test_method_exists_store(): void
    {
        $this->assertTrue(method_exists(Form::class, 'store'));
    }

    public function test_method_exists_update(): void
    {
        $this->assertTrue(method_exists(Form::class, 'update'));
    }

    public function test_method_exists_destroy(): void
    {
        $this->assertTrue(method_exists(Form::class, 'destroy'));
    }

    public function test_method_exists_render(): void
    {
        $this->assertTrue(method_exists(Form::class, 'render'));
    }

    public function test_method_exists_confirm(): void
    {
        $this->assertTrue(method_exists(Form::class, 'confirm'));
    }

    public function test_method_exists_saved(): void
    {
        $this->assertTrue(method_exists(Form::class, 'saved'));
    }

    public function test_method_exists_saving(): void
    {
        $this->assertTrue(method_exists(Form::class, 'saving'));
    }

    public function test_method_exists_submitted(): void
    {
        $this->assertTrue(method_exists(Form::class, 'submitted'));
    }

    public function test_method_exists_deleting(): void
    {
        $this->assertTrue(method_exists(Form::class, 'deleting'));
    }

    public function test_method_exists_deleted(): void
    {
        $this->assertTrue(method_exists(Form::class, 'deleted'));
    }

    public function test_method_exists_uploaded(): void
    {
        $this->assertTrue(method_exists(Form::class, 'uploaded'));
    }

    public function test_method_exists_uploading(): void
    {
        $this->assertTrue(method_exists(Form::class, 'uploading'));
    }

    public function test_method_exists_editing(): void
    {
        $this->assertTrue(method_exists(Form::class, 'editing'));
    }

    public function test_method_exists_creating(): void
    {
        $this->assertTrue(method_exists(Form::class, 'creating'));
    }

    public function test_method_exists_file_deleted(): void
    {
        $this->assertTrue(method_exists(Form::class, 'fileDeleted'));
    }

    public function test_method_exists_file_deleting(): void
    {
        $this->assertTrue(method_exists(Form::class, 'fileDeleting'));
    }

    public function test_builder_property_default_null(): void
    {
        $prop = new ReflectionProperty(Form::class, 'builder');
        $this->assertNull($prop->getDefaultValue());
    }

    public function test_model_property_default_null(): void
    {
        $prop = new ReflectionProperty(Form::class, 'model');
        $this->assertNull($prop->getDefaultValue());
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
