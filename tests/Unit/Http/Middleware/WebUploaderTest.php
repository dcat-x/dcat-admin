<?php

namespace Dcat\Admin\Tests\Unit\Http\Middleware;

use Dcat\Admin\Http\Middleware\WebUploader;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class WebUploaderTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_can_be_instantiated(): void
    {
        $this->assertInstanceOf(WebUploader::class, new WebUploader);
    }

    public function test_handle_passes_through_when_not_uploading(): void
    {
        $middleware = new WebUploader;
        $request = \Illuminate\Http\Request::create('/admin/upload');
        $uploader = Mockery::mock(\Dcat\Admin\Support\WebUploader::class);
        $uploader->shouldReceive('isUploading')->once()->andReturn(false);

        app()->instance('admin.web-uploader', $uploader);

        $called = false;
        $response = $middleware->handle($request, function ($req) use (&$called) {
            $called = true;

            return 'next';
        });

        $this->assertTrue($called);
        $this->assertSame('next', $response);
    }

    public function test_handle_is_public(): void
    {
        $ref = new \ReflectionMethod(WebUploader::class, 'handle');

        $this->assertTrue($ref->isPublic());
    }

    public function test_handle_has_two_parameters(): void
    {
        $ref = new \ReflectionMethod(WebUploader::class, 'handle');
        $params = $ref->getParameters();

        $this->assertCount(2, $params);
    }

    public function test_handle_first_parameter_is_request(): void
    {
        $ref = new \ReflectionMethod(WebUploader::class, 'handle');
        $params = $ref->getParameters();

        $this->assertSame('request', $params[0]->getName());
    }

    public function test_handle_second_parameter_is_next(): void
    {
        $ref = new \ReflectionMethod(WebUploader::class, 'handle');
        $params = $ref->getParameters();

        $this->assertSame('next', $params[1]->getName());
    }

    public function test_handle_request_parameter_is_typed(): void
    {
        $ref = new \ReflectionMethod(WebUploader::class, 'handle');
        $params = $ref->getParameters();

        $this->assertNotNull($params[0]->getType());
        $this->assertSame('Illuminate\Http\Request', $params[0]->getType()->getName());
    }

    public function test_handle_next_parameter_is_closure(): void
    {
        $ref = new \ReflectionMethod(WebUploader::class, 'handle');
        $params = $ref->getParameters();

        $this->assertNotNull($params[1]->getType());
        $this->assertSame('Closure', $params[1]->getType()->getName());
    }
}
