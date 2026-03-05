<?php

namespace Dcat\Admin\Tests\Unit\Http\Controllers;

use Dcat\Admin\Http\Controllers\IconController;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Routing\Controller;
use Mockery;

class IconControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_controller_extends_base_controller(): void
    {
        $controller = new IconController;

        $this->assertInstanceOf(Controller::class, $controller);
    }

    public function test_index_returns_content_with_title(): void
    {
        $content = Mockery::mock(Content::class);
        $content->shouldReceive('title')->with('Icon')->once()->andReturnSelf();
        $content->shouldReceive('body')->once()->andReturnSelf();

        $controller = new IconController;
        $result = $controller->index($content);

        $this->assertSame($content, $result);
    }
}
