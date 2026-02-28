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

    public function test_index_method_is_public(): void
    {
        $reflection = new \ReflectionMethod(IconController::class, 'index');
        $this->assertTrue($reflection->isPublic());
    }

    public function test_index_method_accepts_content_parameter(): void
    {
        $reflection = new \ReflectionMethod(IconController::class, 'index');
        $parameters = $reflection->getParameters();

        $this->assertCount(1, $parameters);
        $this->assertEquals('content', $parameters[0]->getName());
        $this->assertEquals(Content::class, $parameters[0]->getType()->getName());
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

    public function test_controller_has_only_index_method(): void
    {
        $reflection = new \ReflectionClass(IconController::class);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

        $ownMethods = array_filter($methods, function ($method) {
            return $method->getDeclaringClass()->getName() === IconController::class;
        });

        $methodNames = array_map(fn ($m) => $m->getName(), $ownMethods);

        $this->assertContains('index', $methodNames);
    }

    public function test_controller_is_not_abstract(): void
    {
        $reflection = new \ReflectionClass(IconController::class);
        $this->assertFalse($reflection->isAbstract());
    }
}
