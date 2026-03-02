<?php

namespace Dcat\Admin\Tests\Unit\Services;

use Dcat\Admin\Services\AliyunStsService;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class AliyunStsServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(AliyunStsService::class));
    }

    public function test_method_get_sts_token_exists(): void
    {
        $this->assertTrue(method_exists(AliyunStsService::class, 'getStsToken'));
    }

    public function test_method_get_oss_config_exists(): void
    {
        $this->assertTrue(method_exists(AliyunStsService::class, 'getOssConfig'));
    }

    public function test_get_sts_token_is_public(): void
    {
        $reflection = new \ReflectionMethod(AliyunStsService::class, 'getStsToken');
        $this->assertTrue($reflection->isPublic());
    }

    public function test_get_oss_config_is_public(): void
    {
        $reflection = new \ReflectionMethod(AliyunStsService::class, 'getOssConfig');
        $this->assertTrue($reflection->isPublic());
    }

    public function test_get_sts_token_accepts_nullable_string_parameter(): void
    {
        $reflection = new \ReflectionMethod(AliyunStsService::class, 'getStsToken');
        $parameters = $reflection->getParameters();

        $this->assertCount(1, $parameters);
        $this->assertEquals('uploadDir', $parameters[0]->getName());
        $this->assertTrue($parameters[0]->allowsNull());
        $this->assertTrue($parameters[0]->isOptional());
    }

    public function test_get_oss_config_returns_array(): void
    {
        $reflection = new \ReflectionMethod(AliyunStsService::class, 'getOssConfig');
        $returnType = $reflection->getReturnType();

        $this->assertNotNull($returnType);
        $this->assertEquals('array', $returnType->getName());
    }

    public function test_get_oss_config_result(): void
    {
        $service = new AliyunStsService;
        $config = $service->getOssConfig();

        $this->assertIsArray($config);
        $this->assertArrayHasKey('region', $config);
        $this->assertArrayHasKey('bucket', $config);
        $this->assertArrayHasKey('endpoint', $config);
        $this->assertArrayHasKey('cdn_domain', $config);
    }

    public function test_generate_upload_policy_is_protected(): void
    {
        $this->assertTrue(method_exists(AliyunStsService::class, 'generateUploadPolicy'));

        $reflection = new \ReflectionMethod(AliyunStsService::class, 'generateUploadPolicy');
        $this->assertTrue($reflection->isProtected());
    }
}
