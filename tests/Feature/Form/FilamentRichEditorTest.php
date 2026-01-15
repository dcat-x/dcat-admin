<?php

namespace Dcat\Admin\Tests\Feature\Form;

use Dcat\Admin\Form;
use Dcat\Admin\Form\Field\Filament\RichEditor;
use Dcat\Admin\Repositories\EloquentRepository;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;

class RichEditorTestModel extends Model
{
    protected $table = 'test_articles';

    protected $primaryKey = 'id';

    protected $fillable = ['content'];

    public $timestamps = true;
}

class RichEditorTestRepository extends EloquentRepository
{
    protected $eloquentClass = RichEditorTestModel::class;
}

class FilamentRichEditorTest extends TestCase
{
    public function test_rich_editor_can_be_added_to_form(): void
    {
        $form = new Form(new RichEditorTestRepository);

        $field = $form->filamentRichEditor('content', 'Content')
            ->disk('public')
            ->directory('uploads')
            ->toolbarButtons(['bold', 'italic']);

        $this->assertInstanceOf(RichEditor::class, $field);
        $this->assertEquals('content', $field->column());
        $this->assertEquals('Content', $field->label());
    }

    public function test_rich_editor_config_is_correctly_set(): void
    {
        $form = new Form(new RichEditorTestRepository);

        $field = $form->filamentRichEditor('content')
            ->disk('s3')
            ->directory('editor-files')
            ->visibility('private');

        $config = $field->getFilamentConfig();

        $this->assertEquals('s3', $config['disk']);
        $this->assertEquals('editor-files', $config['directory']);
        $this->assertEquals('private', $config['visibility']);
    }
}
