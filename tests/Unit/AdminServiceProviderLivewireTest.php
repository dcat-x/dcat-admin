<?php

namespace Dcat\Admin\Tests\Unit;

use Dcat\Admin\Livewire\LiveSelectComponent;
use Dcat\Admin\Livewire\RichEditorComponent;
use Dcat\Admin\Tests\TestCase;
use Livewire\Mechanisms\ComponentRegistry;

class AdminServiceProviderLivewireTest extends TestCase
{
    public function test_rich_editor_component_is_registered(): void
    {
        // Test that the component class can be resolved by name
        $registry = app(ComponentRegistry::class);
        $class = $registry->getClass('dcat-admin::rich-editor');

        $this->assertEquals(RichEditorComponent::class, $class);
    }

    public function test_live_select_component_is_registered(): void
    {
        // Test that the component class can be resolved by name
        $registry = app(ComponentRegistry::class);
        $class = $registry->getClass('dcat-admin::live-select');

        $this->assertEquals(LiveSelectComponent::class, $class);
    }
}
