<?php

namespace Dcat\Admin\Tests\Unit\Form;

use Dcat\Admin\Form;
use Dcat\Admin\Form\Field\Filament\LiveSelect;
use Dcat\Admin\Form\Field\Filament\RichEditor;
use Dcat\Admin\Tests\TestCase;

class FilamentFieldRegistrationTest extends TestCase
{
    public function test_filament_rich_editor_in_available_fields(): void
    {
        $this->assertTrue(
            array_key_exists('filamentRichEditor', Form::extensions())
            || method_exists(Form::class, 'filamentRichEditor')
        );

        // Direct instantiation test
        $field = new RichEditor('content');
        $this->assertInstanceOf(RichEditor::class, $field);
        $this->assertEquals('content', $field->column());
    }

    public function test_filament_live_select_in_available_fields(): void
    {
        $this->assertTrue(
            array_key_exists('filamentLiveSelect', Form::extensions())
            || method_exists(Form::class, 'filamentLiveSelect')
        );

        // Direct instantiation test
        $field = new LiveSelect('category_id');
        $this->assertInstanceOf(LiveSelect::class, $field);
        $this->assertEquals('category_id', $field->column());
    }
}
