<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Http\Controllers;

use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\ExtensionController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class ExtensionControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_index_builds_content_with_grid_body(): void
    {
        $content = Mockery::mock(Content::class);
        $content->shouldReceive('title')->once()->andReturnSelf();
        $content->shouldReceive('description')->once()->andReturnSelf();
        $content->shouldReceive('body')->once()->with(Mockery::type(Grid::class))->andReturnSelf();

        $controller = new ExtensionController;
        $result = $controller->index($content);

        $this->assertSame($content, $result);
    }

    public function test_form_returns_form_instance(): void
    {
        $controller = new ExtensionController;

        $this->assertInstanceOf(Form::class, $controller->form());
    }

    public function test_grid_builder_returns_grid_instance(): void
    {
        $controller = new class extends ExtensionController
        {
            public function exposeGrid(): Grid
            {
                return $this->grid();
            }
        };

        $this->assertInstanceOf(Grid::class, $controller->exposeGrid());
    }

    public function test_create_extension_returns_preformatted_output_string(): void
    {
        $controller = new ExtensionController;
        $result = $controller->createExtension('vendor/demo', 'Demo\\Package', 2);

        $this->assertStringContainsString('<pre', $result);
        $this->assertStringContainsString('text-white', $result);
    }
}
