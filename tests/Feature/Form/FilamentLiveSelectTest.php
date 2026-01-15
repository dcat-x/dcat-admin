<?php

namespace Dcat\Admin\Tests\Feature\Form;

use Dcat\Admin\Form;
use Dcat\Admin\Form\Field\Filament\LiveSelect;
use Dcat\Admin\Repositories\EloquentRepository;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;

class LiveSelectTestModel extends Model
{
    protected $table = 'test_articles';

    protected $primaryKey = 'id';

    protected $fillable = ['category_id', 'status', 'tags'];

    public $timestamps = true;
}

class LiveSelectTestRepository extends EloquentRepository
{
    protected $eloquentClass = LiveSelectTestModel::class;
}

class FilamentLiveSelectTest extends TestCase
{
    public function test_live_select_can_be_added_to_form(): void
    {
        $form = new Form(new LiveSelectTestRepository);

        $field = $form->filamentLiveSelect('category_id', 'Category')
            ->searchUrl('/api/categories/search')
            ->valueField('id')
            ->labelField('name');

        $this->assertInstanceOf(LiveSelect::class, $field);
        $this->assertEquals('category_id', $field->column());
        $this->assertEquals('Category', $field->label());
    }

    public function test_live_select_with_static_options(): void
    {
        $form = new Form(new LiveSelectTestRepository);

        $field = $form->filamentLiveSelect('status')
            ->options([
                'active' => 'Active',
                'inactive' => 'Inactive',
            ]);

        $config = $field->getFilamentConfig();

        $this->assertArrayHasKey('options', $config);
        $this->assertCount(2, $config['options']);
    }

    public function test_live_select_multiple(): void
    {
        $form = new Form(new LiveSelectTestRepository);

        $field = $form->filamentLiveSelect('tags')
            ->multiple()
            ->searchUrl('/api/tags/search');

        $config = $field->getFilamentConfig();

        $this->assertTrue($config['multiple']);
    }
}
