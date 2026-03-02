<?php

namespace Dcat\Admin\Tests\Unit\Widgets;

use Dcat\Admin\Form\Concerns\HandleCascadeFields;
use Dcat\Admin\Form\Concerns\HasLayout;
use Dcat\Admin\Form\Concerns\HasRows;
use Dcat\Admin\Form\Concerns\HasTabs;
use Dcat\Admin\Form\ResolveField;
use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Traits\HasAuthorization;
use Dcat\Admin\Traits\HasFormResponse;
use Dcat\Admin\Traits\HasHtmlAttributes;
use Dcat\Admin\Widgets\Form;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Traits\Macroable;
use Mockery;

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
        $reflection = new \ReflectionClass(Form::class);
        $this->assertTrue($reflection->implementsInterface(Renderable::class));
    }

    public function test_uses_macroable_trait(): void
    {
        $traits = class_uses_recursive(Form::class);
        $this->assertContains(Macroable::class, $traits);
    }

    public function test_uses_has_rows_trait(): void
    {
        $traits = class_uses_recursive(Form::class);
        $this->assertContains(HasRows::class, $traits);
    }

    public function test_uses_has_tabs_trait(): void
    {
        $traits = class_uses_recursive(Form::class);
        $this->assertContains(HasTabs::class, $traits);
    }

    public function test_uses_has_layout_trait(): void
    {
        $traits = class_uses_recursive(Form::class);
        $this->assertContains(HasLayout::class, $traits);
    }

    public function test_uses_handle_cascade_fields_trait(): void
    {
        $traits = class_uses_recursive(Form::class);
        $this->assertContains(HandleCascadeFields::class, $traits);
    }

    public function test_uses_resolve_field_trait(): void
    {
        $traits = class_uses_recursive(Form::class);
        $this->assertContains(ResolveField::class, $traits);
    }

    public function test_uses_has_authorization_trait(): void
    {
        $traits = class_uses_recursive(Form::class);
        $this->assertContains(HasAuthorization::class, $traits);
    }

    public function test_uses_has_form_response_trait(): void
    {
        $traits = class_uses_recursive(Form::class);
        $this->assertContains(HasFormResponse::class, $traits);
    }

    public function test_uses_has_html_attributes_trait(): void
    {
        $traits = class_uses_recursive(Form::class);
        $this->assertContains(HasHtmlAttributes::class, $traits);
    }

    public function test_method_method_exists(): void
    {
        $this->assertTrue(method_exists(Form::class, 'method'));
    }

    public function test_method_action_exists(): void
    {
        $this->assertTrue(method_exists(Form::class, 'action'));
    }

    public function test_method_fill_exists(): void
    {
        $this->assertTrue(method_exists(Form::class, 'fill'));
    }

    public function test_method_data_exists(): void
    {
        $this->assertTrue(method_exists(Form::class, 'data'));
    }

    public function test_method_confirm_exists(): void
    {
        $this->assertTrue(method_exists(Form::class, 'confirm'));
    }

    public function test_method_disable_reset_button_exists(): void
    {
        $this->assertTrue(method_exists(Form::class, 'disableResetButton'));
    }

    public function test_method_disable_submit_button_exists(): void
    {
        $this->assertTrue(method_exists(Form::class, 'disableSubmitButton'));
    }

    public function test_method_width_exists(): void
    {
        $this->assertTrue(method_exists(Form::class, 'width'));
    }

    public function test_method_ajax_exists(): void
    {
        $this->assertTrue(method_exists(Form::class, 'ajax'));
    }

    public function test_method_render_exists(): void
    {
        $this->assertTrue(method_exists(Form::class, 'render'));
    }

    public function test_form_creation(): void
    {
        $form = new Form;
        $this->assertInstanceOf(Form::class, $form);
    }

    public function test_form_fill_with_array(): void
    {
        $form = new Form;
        $result = $form->fill(['name' => 'test']);
        $this->assertInstanceOf(Form::class, $result);
    }

    public function test_form_data_returns_fluent(): void
    {
        $form = new Form(['key' => 'value']);
        $data = $form->data();
        $this->assertNotNull($data);
    }

    public function test_form_default_ajax_property(): void
    {
        $form = new Form;
        $reflection = new \ReflectionProperty(Form::class, 'ajax');
        $reflection->setAccessible(true);
        $this->assertTrue($reflection->getValue($form));
    }

    public function test_form_ajax_method(): void
    {
        $form = new Form;
        $result = $form->ajax(false);
        $this->assertInstanceOf(Form::class, $result);
        $this->assertFalse($form->allowAjaxSubmit());
    }

    public function test_form_confirm_method(): void
    {
        $form = new Form;
        $result = $form->confirm('Are you sure?', 'Confirmation');
        $this->assertInstanceOf(Form::class, $result);

        $reflection = new \ReflectionProperty(Form::class, 'confirm');
        $reflection->setAccessible(true);
        $confirmValue = $reflection->getValue($form);
        $this->assertEquals('Are you sure?', $confirmValue['title']);
        $this->assertEquals('Confirmation', $confirmValue['content']);
    }

    public function test_form_width_method(): void
    {
        $form = new Form;
        $result = $form->width(6, 3);
        $this->assertInstanceOf(Form::class, $result);

        $reflection = new \ReflectionProperty(Form::class, 'width');
        $reflection->setAccessible(true);
        $widthValue = $reflection->getValue($form);
        $this->assertEquals(['label' => 3, 'field' => 6], $widthValue);
    }

    public function test_form_disable_reset_button(): void
    {
        $form = new Form;
        $result = $form->disableResetButton();
        $this->assertInstanceOf(Form::class, $result);

        $reflection = new \ReflectionProperty(Form::class, 'buttons');
        $reflection->setAccessible(true);
        $buttons = $reflection->getValue($form);
        $this->assertFalse($buttons['reset']);
    }

    public function test_form_disable_submit_button(): void
    {
        $form = new Form;
        $result = $form->disableSubmitButton();
        $this->assertInstanceOf(Form::class, $result);

        $reflection = new \ReflectionProperty(Form::class, 'buttons');
        $reflection->setAccessible(true);
        $buttons = $reflection->getValue($form);
        $this->assertFalse($buttons['submit']);
    }

    public function test_form_default_buttons(): void
    {
        $form = new Form;
        $reflection = new \ReflectionProperty(Form::class, 'buttons');
        $reflection->setAccessible(true);
        $buttons = $reflection->getValue($form);

        $this->assertTrue($buttons['reset']);
        $this->assertTrue($buttons['submit']);
    }

    public function test_form_set_and_get_key(): void
    {
        $form = new Form;
        $form->setKey(42);
        $this->assertEquals(42, $form->getKey());
    }

    public function test_form_static_make(): void
    {
        $form = Form::make(['name' => 'test']);
        $this->assertInstanceOf(Form::class, $form);
    }

    public function test_form_request_name_constant(): void
    {
        $this->assertEquals('_form_', Form::REQUEST_NAME);
    }

    public function test_form_current_url_name_constant(): void
    {
        $this->assertEquals('_current_', Form::CURRENT_URL_NAME);
    }

    public function test_form_lazy_payload_name_constant(): void
    {
        $this->assertEquals('_payload_', Form::LAZY_PAYLOAD_NAME);
    }
}
