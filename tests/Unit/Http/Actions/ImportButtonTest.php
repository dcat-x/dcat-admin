<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Http\Actions;

use Dcat\Admin\Grid\RowAction;
use Dcat\Admin\Http\Actions\ImportButton;
use Dcat\Admin\Layout\Asset;
use Dcat\Admin\Tests\TestCase;

class ImportButtonTest extends TestCase
{
    public function test_render_outputs_button_with_key_and_import_text(): void
    {
        $this->app->instance('admin.asset', new Asset);

        $action = new ImportButton;
        $action->setKey('demo-ext');

        $html = $action->render();

        $this->assertInstanceOf(RowAction::class, $action);
        $this->assertStringContainsString('class="import-extension"', $html);
        $this->assertStringContainsString('data-id="demo-ext"', $html);
        $this->assertStringContainsString(trans('admin.import'), $html);
    }

    public function test_render_registers_import_script(): void
    {
        $asset = new Asset;
        $this->app->instance('admin.asset', $asset);

        $action = new ImportButton;
        $action->setKey('demo-ext');
        $action->render();

        $this->assertNotEmpty($asset->script);
        $script = implode("\n", $asset->script);
        $this->assertStringContainsString('.import-extension', $script);
        $this->assertStringContainsString('helpers/extensions/import', $script);
    }
}
