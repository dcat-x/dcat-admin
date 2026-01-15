<?php

namespace Dcat\Admin\Tests\Unit\Livewire;

use Dcat\Admin\Livewire\LiveSelectComponent;
use Dcat\Admin\Tests\TestCase;
use Filament\Forms\Contracts\HasForms;

class LiveSelectComponentTest extends TestCase
{
    public function test_component_implements_has_forms(): void
    {
        $this->assertTrue(
            in_array(HasForms::class, class_implements(LiveSelectComponent::class))
        );
    }

    public function test_component_can_be_instantiated(): void
    {
        $component = new LiveSelectComponent;
        $this->assertInstanceOf(LiveSelectComponent::class, $component);
    }

    public function test_component_has_required_properties(): void
    {
        $component = new LiveSelectComponent;

        $reflection = new \ReflectionClass($component);

        $this->assertTrue($reflection->hasProperty('fieldName'));
        $this->assertTrue($reflection->hasProperty('fieldValue'));
        $this->assertTrue($reflection->hasProperty('fieldConfig'));
        $this->assertTrue($reflection->hasProperty('fieldDisabled'));
        $this->assertTrue($reflection->hasProperty('data'));
    }

    public function test_component_has_form_method(): void
    {
        $component = new LiveSelectComponent;

        $this->assertTrue(method_exists($component, 'form'));
    }

    public function test_component_has_render_method(): void
    {
        $component = new LiveSelectComponent;

        $this->assertTrue(method_exists($component, 'render'));
    }
}
