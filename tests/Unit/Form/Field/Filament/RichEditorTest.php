<?php

namespace Dcat\Admin\Tests\Unit\Form\Field\Filament;

use Dcat\Admin\Form\Field\Filament\RichEditor;
use Dcat\Admin\Tests\TestCase;

class RichEditorTest extends TestCase
{
    public function test_rich_editor_extends_filament_field(): void
    {
        $field = new RichEditor('content');
        $this->assertInstanceOf(\Dcat\Admin\Form\Field\Filament\FilamentField::class, $field);
    }

    public function test_rich_editor_has_correct_livewire_component(): void
    {
        $field = new RichEditor('content');
        $this->assertEquals('dcat-admin::rich-editor', $field->getLivewireComponent());
    }

    public function test_rich_editor_can_set_disk(): void
    {
        $field = new RichEditor('content');
        $field->disk('s3');

        $config = $field->getFilamentConfig();
        $this->assertEquals('s3', $config['disk']);
    }

    public function test_rich_editor_can_set_directory(): void
    {
        $field = new RichEditor('content');
        $field->directory('uploads/editor');

        $config = $field->getFilamentConfig();
        $this->assertEquals('uploads/editor', $config['directory']);
    }

    public function test_rich_editor_can_set_toolbar_buttons(): void
    {
        $field = new RichEditor('content');
        $field->toolbarButtons(['bold', 'italic', 'link']);

        $config = $field->getFilamentConfig();
        $this->assertEquals(['bold', 'italic', 'link'], $config['toolbarButtons']);
    }

    public function test_rich_editor_can_disable_attachments(): void
    {
        $field = new RichEditor('content');
        $field->disableAttachments();

        $config = $field->getFilamentConfig();
        $this->assertNotContains('attachFiles', $config['toolbarButtons']);
    }
}
