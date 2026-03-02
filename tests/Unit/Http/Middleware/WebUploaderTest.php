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

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(WebUploader::class));
    }

    public function test_method_handle_exists(): void
    {
        $this->assertTrue(method_exists(WebUploader::class, 'handle'));
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

        $this->assertEquals('request', $params[0]->getName());
    }

    public function test_handle_second_parameter_is_next(): void
    {
        $ref = new \ReflectionMethod(WebUploader::class, 'handle');
        $params = $ref->getParameters();

        $this->assertEquals('next', $params[1]->getName());
    }

    public function test_handle_request_parameter_is_typed(): void
    {
        $ref = new \ReflectionMethod(WebUploader::class, 'handle');
        $params = $ref->getParameters();

        $this->assertNotNull($params[0]->getType());
        $this->assertEquals('Illuminate\Http\Request', $params[0]->getType()->getName());
    }

    public function test_handle_next_parameter_is_closure(): void
    {
        $ref = new \ReflectionMethod(WebUploader::class, 'handle');
        $params = $ref->getParameters();

        $this->assertNotNull($params[1]->getType());
        $this->assertEquals('Closure', $params[1]->getType()->getName());
    }
}
