<?php

namespace Dcat\Admin\Tests\Unit\Livewire;

use Dcat\Admin\Livewire\LiveSelectComponent;
use Dcat\Admin\Tests\TestCase;
use Livewire\Livewire;

class LiveSelectComponentTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (class_exists(LiveSelectComponent::class)) {
            Livewire::component('dcat-admin::live-select', LiveSelectComponent::class);
        }
    }

    public function test_component_can_be_rendered(): void
    {
        Livewire::test(LiveSelectComponent::class, [
            'fieldName' => 'category_id',
            'fieldValue' => null,
            'fieldConfig' => [],
            'fieldDisabled' => false,
        ])->assertStatus(200);
    }

    public function test_component_mounts_with_initial_value(): void
    {
        Livewire::test(LiveSelectComponent::class, [
            'fieldName' => 'category_id',
            'fieldValue' => 5,
            'fieldConfig' => [],
            'fieldDisabled' => false,
        ])
        ->assertSet('fieldName', 'category_id')
        ->assertSet('fieldValue', 5);
    }

    public function test_component_respects_disabled_state(): void
    {
        Livewire::test(LiveSelectComponent::class, [
            'fieldName' => 'category_id',
            'fieldValue' => null,
            'fieldConfig' => [],
            'fieldDisabled' => true,
        ])
        ->assertSet('fieldDisabled', true);
    }

    public function test_component_accepts_static_options(): void
    {
        Livewire::test(LiveSelectComponent::class, [
            'fieldName' => 'status',
            'fieldValue' => null,
            'fieldConfig' => [
                'options' => ['active' => 'Active', 'inactive' => 'Inactive'],
            ],
            'fieldDisabled' => false,
        ])
        ->assertSet('fieldConfig.options', ['active' => 'Active', 'inactive' => 'Inactive']);
    }
}
