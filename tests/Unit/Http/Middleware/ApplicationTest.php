<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Http\Middleware;

use Dcat\Admin\Http\Middleware\Application;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class ApplicationTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_can_be_instantiated(): void
    {
        $this->assertInstanceOf(Application::class, new Application);
    }

    public function test_handle_is_public(): void
    {
        $ref = new \ReflectionMethod(Application::class, 'handle');

        $this->assertTrue($ref->isPublic());
    }

    public function test_handle_has_three_parameters(): void
    {
        $ref = new \ReflectionMethod(Application::class, 'handle');
        $params = $ref->getParameters();

        $this->assertCount(3, $params);
    }

    public function test_handle_first_parameter_is_request(): void
    {
        $ref = new \ReflectionMethod(Application::class, 'handle');
        $params = $ref->getParameters();

        $this->assertSame('request', $params[0]->getName());
    }

    public function test_handle_second_parameter_is_next(): void
    {
        $ref = new \ReflectionMethod(Application::class, 'handle');
        $params = $ref->getParameters();

        $this->assertSame('next', $params[1]->getName());
    }

    public function test_handle_third_parameter_is_app(): void
    {
        $ref = new \ReflectionMethod(Application::class, 'handle');
        $params = $ref->getParameters();

        $this->assertSame('app', $params[2]->getName());
    }

    public function test_handle_app_parameter_has_null_default(): void
    {
        $ref = new \ReflectionMethod(Application::class, 'handle');
        $params = $ref->getParameters();

        $this->assertTrue($params[2]->isDefaultValueAvailable());
        $this->assertNull($params[2]->getDefaultValue());
    }

    public function test_handle_next_parameter_is_closure(): void
    {
        $ref = new \ReflectionMethod(Application::class, 'handle');
        $params = $ref->getParameters();

        $this->assertNotNull($params[1]->getType());
        $this->assertSame('Closure', $params[1]->getType()->getName());
    }

    public function test_handle_passes_through_when_app_is_null(): void
    {
        $middleware = new Application;
        $request = \Illuminate\Http\Request::create('/admin/test');

        $called = false;
        $response = $middleware->handle($request, function ($req) use (&$called) {
            $called = true;

            return 'next';
        });

        $this->assertTrue($called);
        $this->assertSame('next', $response);
    }
}
