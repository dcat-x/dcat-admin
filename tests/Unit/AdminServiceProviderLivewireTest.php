<?php

namespace Dcat\Admin\Tests\Unit;

use Dcat\Admin\Livewire\LiveSelectComponent;
use Dcat\Admin\Livewire\RichEditorComponent;
use Dcat\Admin\Tests\TestCase;
use Livewire\Livewire;

class AdminServiceProviderLivewireTest extends TestCase
{
    public function test_rich_editor_component_is_registered(): void
    {
        // Test by attempting to render the component
        Livewire::test(RichEditorComponent::class, [
            'fieldName' => 'test',
            'fieldValue' => '',
            'fieldConfig' => [],
            'fieldDisabled' => false,
        ])->assertStatus(200);
    }

    public function test_live_select_component_is_registered(): void
    {
        // Test by attempting to render the component
        Livewire::test(LiveSelectComponent::class, [
            'fieldName' => 'test',
            'fieldValue' => null,
            'fieldConfig' => [],
            'fieldDisabled' => false,
        ])->assertStatus(200);
    }
}
