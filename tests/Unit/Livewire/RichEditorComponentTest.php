<?php

namespace Dcat\Admin\Tests\Unit\Livewire;

use Dcat\Admin\Livewire\RichEditorComponent;
use Dcat\Admin\Tests\TestCase;
use Livewire\Livewire;

class RichEditorComponentTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Register the component for testing
        if (class_exists(RichEditorComponent::class)) {
            Livewire::component('dcat-admin::rich-editor', RichEditorComponent::class);
        }
    }

    public function test_component_can_be_rendered(): void
    {
        Livewire::test(RichEditorComponent::class, [
            'fieldName' => 'content',
            'fieldValue' => '<p>Hello</p>',
            'fieldConfig' => [],
            'fieldDisabled' => false,
        ])->assertStatus(200);
    }

    public function test_component_mounts_with_initial_value(): void
    {
        Livewire::test(RichEditorComponent::class, [
            'fieldName' => 'content',
            'fieldValue' => '<p>Initial content</p>',
            'fieldConfig' => [],
            'fieldDisabled' => false,
        ])
        ->assertSet('fieldName', 'content')
        ->assertSet('fieldValue', '<p>Initial content</p>');
    }

    public function test_component_respects_disabled_state(): void
    {
        Livewire::test(RichEditorComponent::class, [
            'fieldName' => 'content',
            'fieldValue' => '',
            'fieldConfig' => [],
            'fieldDisabled' => true,
        ])
        ->assertSet('fieldDisabled', true);
    }
}
