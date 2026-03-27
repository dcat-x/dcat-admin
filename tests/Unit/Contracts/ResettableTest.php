<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Contracts;

use Dcat\Admin\Contracts\Resettable;
use Dcat\Admin\Form;
use Dcat\Admin\Grid\Column;
use Dcat\Admin\Http\Controllers\ImportController;
use Dcat\Admin\Support\JavaScript;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Support\Collection;

class ResettableTest extends TestCase
{
    public function test_import_controller_implements_resettable(): void
    {
        $this->assertInstanceOf(Resettable::class, new ImportController);
    }

    public function test_import_controller_reset_clears_registry(): void
    {
        ImportController::registerImporter('test', ['titles' => ['a' => 'A']]);
        ImportController::resetState();

        $ref = new \ReflectionProperty(ImportController::class, 'importerRegistry');
        $this->assertSame([], $ref->getValue());
    }

    public function test_javascript_implements_resettable(): void
    {
        $this->assertContains(Resettable::class, class_implements(JavaScript::class));
    }

    public function test_javascript_reset_clears_scripts(): void
    {
        JavaScript::make('alert(1)');
        $this->assertNotEmpty(JavaScript::all());

        JavaScript::resetState();
        $this->assertSame([], JavaScript::all());
    }

    public function test_form_implements_resettable(): void
    {
        $this->assertContains(Resettable::class, class_implements(Form::class));
    }

    public function test_form_reset_clears_collected_assets(): void
    {
        $ref = new \ReflectionProperty(Form::class, 'collectedAssets');
        $ref->setValue(null, ['test-asset']);

        Form::resetState();
        $this->assertSame([], $ref->getValue());
    }

    public function test_column_implements_resettable(): void
    {
        $this->assertContains(Resettable::class, class_implements(Column::class));
    }

    public function test_column_reset_clears_original_grid_models(): void
    {
        Column::setOriginalGridModels(new Collection([['id' => 1]]));

        $ref = new \ReflectionProperty(Column::class, 'originalGridModels');
        $this->assertNotNull($ref->getValue());

        Column::resetState();
        $this->assertNull($ref->getValue());
    }
}
