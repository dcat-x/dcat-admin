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

    public function test_action_title_and_resource_work_as_expected(): void
    {
        $form = new Form;

        $this->assertSame($form, $form->action('users'));
        $this->assertStringContainsString('/users', $form->action());

        $this->assertSame($form, $form->title('Custom title'));
        $this->assertSame('Custom title', $form->builder()->title());

        $this->assertSame($form, $form->setResource('users'));
        $this->assertStringContainsString('/users', $form->resource(0));
    }

    public function test_builder_and_model_accessors_return_instances(): void
    {
        $form = new Form;

        $this->assertInstanceOf(\Dcat\Admin\Form\Builder::class, $form->builder());
        $this->assertInstanceOf(\Illuminate\Support\Fluent::class, $form->model());
    }

    public function test_disable_methods_are_chainable_and_affect_footer_rendering(): void
    {
        $form = new Form;

        $result = $form
            ->disableHeader()
            ->disableViewCheck()
            ->disableEditingCheck()
            ->disableCreatingCheck();

        $this->assertSame($form, $result);

        $footer = $form->builder()->footer()->render();
        $this->assertStringNotContainsString('after-save', $footer);
    }

    public function test_form_mode_helpers_reflect_builder_mode(): void
    {
        $form = new Form;

        $this->assertTrue($form->isCreating());
        $this->assertFalse($form->isEditing());
        $this->assertFalse($form->isDeleting());
    }

    public function test_event_registration_methods_are_chainable(): void
    {
        $form = new Form;

        $result = $form
            ->creating(fn () => null)
            ->editing(fn () => null)
            ->submitted(fn () => null)
            ->saving(fn () => null)
            ->saved(fn () => null)
            ->deleting(fn () => null)
            ->deleted(fn () => null)
            ->uploading(fn () => null)
            ->uploaded(fn () => null)
            ->fileDeleting(fn () => null)
            ->fileDeleted(fn () => null);

        $this->assertSame($form, $result);
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
