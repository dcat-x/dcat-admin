<?php

namespace Dcat\Admin\Tests\Unit\Form\Field\Filament;

use Dcat\Admin\Form\Field\Filament\FilamentField;
use Dcat\Admin\Tests\TestCase;

class FilamentFieldTest extends TestCase
{
    public function test_filament_field_extends_field(): void
    {
        $field = new class('content') extends FilamentField {
            protected string $livewireComponent = 'test-component';
        };
        $this->assertInstanceOf(\Dcat\Admin\Form\Field::class, $field);
    }

    public function test_filament_field_has_livewire_component_name(): void
    {
        $field = new class('content') extends FilamentField {
            protected string $livewireComponent = 'test-component';
        };

        $this->assertEquals('test-component', $field->getLivewireComponent());
    }

    public function test_filament_field_generates_wire_key(): void
    {
        $field = new class('content') extends FilamentField {
            protected string $livewireComponent = 'test-component';
        };

        $wireKey = $field->getWireKey();
        $this->assertNotEmpty($wireKey);
        $this->assertStringContainsString('content', $wireKey);
    }

    public function test_filament_field_can_set_config(): void
    {
        $field = new class('content') extends FilamentField {
            protected string $livewireComponent = 'test-component';
        };

        $field->filamentConfig(['disk' => 's3', 'directory' => 'uploads']);
        $config = $field->getFilamentConfig();

        $this->assertEquals('s3', $config['disk']);
        $this->assertEquals('uploads', $config['directory']);
    }
}
