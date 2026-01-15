<?php

namespace Dcat\Admin\Tests\Unit\Form\Field\Filament;

use Dcat\Admin\Form\Field\Filament\LiveSelect;
use Dcat\Admin\Tests\TestCase;

class LiveSelectTest extends TestCase
{
    public function test_live_select_extends_filament_field(): void
    {
        $field = new LiveSelect('category_id');
        $this->assertInstanceOf(\Dcat\Admin\Form\Field\Filament\FilamentField::class, $field);
    }

    public function test_live_select_has_correct_livewire_component(): void
    {
        $field = new LiveSelect('category_id');
        $this->assertEquals('dcat-admin::live-select', $field->getLivewireComponent());
    }

    public function test_live_select_can_set_search_url(): void
    {
        $field = new LiveSelect('category_id');
        $field->searchUrl('/api/categories/search');

        $config = $field->getFilamentConfig();
        $this->assertEquals('/api/categories/search', $config['searchUrl']);
    }

    public function test_live_select_can_set_value_and_label_fields(): void
    {
        $field = new LiveSelect('category_id');
        $field->valueField('code')->labelField('title');

        $config = $field->getFilamentConfig();
        $this->assertEquals('code', $config['valueField']);
        $this->assertEquals('title', $config['labelField']);
    }

    public function test_live_select_can_set_static_options(): void
    {
        $field = new LiveSelect('status');
        $field->options(['active' => 'Active', 'inactive' => 'Inactive']);

        $config = $field->getFilamentConfig();
        $this->assertEquals(['active' => 'Active', 'inactive' => 'Inactive'], $config['options']);
    }

    public function test_live_select_can_enable_multiple(): void
    {
        $field = new LiveSelect('tags');
        $field->multiple();

        $config = $field->getFilamentConfig();
        $this->assertTrue($config['multiple']);
    }

    public function test_live_select_can_set_min_chars(): void
    {
        $field = new LiveSelect('category_id');
        $field->minChars(3);

        $config = $field->getFilamentConfig();
        $this->assertEquals(3, $config['minChars']);
    }

    public function test_live_select_can_enable_preload(): void
    {
        $field = new LiveSelect('category_id');
        $field->preload();

        $config = $field->getFilamentConfig();
        $this->assertTrue($config['preload']);
    }
}
