<?php

namespace Dcat\Admin\Tests\Unit\Http\Middleware;

use Dcat\Admin\Http\Middleware\Bootstrap;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BootstrapTest extends TestCase
{
    public function test_handle_returns_response(): void
    {
        $middleware = new Bootstrap;
        $request = Request::create('/admin/test', 'GET');
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');

        $response = new Response('OK', 200);
        $result = $middleware->handle($request, function () use ($response) {
            return $response;
        });

        $this->assertSame($response, $result);
    }

    public function test_handle_calls_next_closure(): void
    {
        $middleware = new Bootstrap;
        $request = Request::create('/admin/test', 'GET');
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');

        $called = false;
        $middleware->handle($request, function ($req) use (&$called) {
            $called = true;

            return new Response('OK');
        });

        $this->assertTrue($called);
    }

    public function test_prefetch_detection_via_x_moz_header(): void
    {
        $middleware = new Bootstrap;
        $request = Request::create('/admin/test', 'GET');
        $request->server->set('HTTP_X_MOZ', 'prefetch');

        $this->assertTrue($middleware->prefetch($request));
    }

    public function test_prefetch_detection_via_purpose_header(): void
    {
        $middleware = new Bootstrap;
        $request = Request::create('/admin/test', 'GET');
        $request->headers->set('Purpose', 'prefetch');

        $this->assertTrue($middleware->prefetch($request));
    }

    public function test_prefetch_detection_case_insensitive(): void
    {
        $middleware = new Bootstrap;
        $request = Request::create('/admin/test', 'GET');
        $request->headers->set('Purpose', 'Prefetch');

        $this->assertTrue($middleware->prefetch($request));
    }

    public function test_non_prefetch_request(): void
    {
        $middleware = new Bootstrap;
        $request = Request::create('/admin/test', 'GET');

        $this->assertFalse($middleware->prefetch($request));
    }

    public function test_include_bootstrap_file_method_exists(): void
    {
        $middleware = new Bootstrap;

        $reflection = new \ReflectionClass($middleware);
        $method = $reflection->getMethod('includeBootstrapFile');

        $this->assertTrue($method->isProtected());
    }

    public function test_add_script_method_exists(): void
    {
        $middleware = new Bootstrap;

        $reflection = new \ReflectionClass($middleware);
        $method = $reflection->getMethod('addScript');

        $this->assertTrue($method->isProtected());
    }

    public function test_fire_events_method_exists(): void
    {
        $middleware = new Bootstrap;

        $reflection = new \ReflectionClass($middleware);
        $method = $reflection->getMethod('fireEvents');

        $this->assertTrue($method->isProtected());
    }

    public function test_store_current_url_method_exists(): void
    {
        $middleware = new Bootstrap;

        $reflection = new \ReflectionClass($middleware);
        $method = $reflection->getMethod('storeCurrentUrl');

        $this->assertTrue($method->isProtected());
    }
}
