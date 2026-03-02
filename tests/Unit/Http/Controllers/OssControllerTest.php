<?php

namespace Dcat\Admin\Tests\Unit\Http\Controllers;

use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Http\Controllers\OssController;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class OssControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(OssController::class));
    }

    public function test_is_subclass_of_admin_controller(): void
    {
        $this->assertTrue(is_subclass_of(OssController::class, AdminController::class));
    }

    public function test_method_get_sts_token_exists(): void
    {
        $this->assertTrue(method_exists(OssController::class, 'getStsToken'));
    }

    public function test_method_generate_upload_dir_exists(): void
    {
        $this->assertTrue(method_exists(OssController::class, 'generateUploadDir'));
    }

    public function test_method_generate_date_path_exists(): void
    {
        $this->assertTrue(method_exists(OssController::class, 'generateDatePath'));
    }

    public function test_method_validate_and_format_directory_exists(): void
    {
        $this->assertTrue(method_exists(OssController::class, 'validateAndFormatDirectory'));
    }

    public function test_method_generate_filename_exists(): void
    {
        $this->assertTrue(method_exists(OssController::class, 'generateFilename'));
    }

    public function test_method_private_image_proxy_exists(): void
    {
        $this->assertTrue(method_exists(OssController::class, 'privateImageProxy'));
    }

    public function test_get_sts_token_is_public(): void
    {
        $ref = new \ReflectionMethod(OssController::class, 'getStsToken');

        $this->assertTrue($ref->isPublic());
    }

    public function test_generate_upload_dir_is_protected(): void
    {
        $ref = new \ReflectionMethod(OssController::class, 'generateUploadDir');

        $this->assertTrue($ref->isProtected());
    }

    public function test_generate_date_path_is_protected(): void
    {
        $ref = new \ReflectionMethod(OssController::class, 'generateDatePath');

        $this->assertTrue($ref->isProtected());
    }

    public function test_validate_and_format_directory_is_protected(): void
    {
        $ref = new \ReflectionMethod(OssController::class, 'validateAndFormatDirectory');

        $this->assertTrue($ref->isProtected());
    }

    public function test_generate_filename_is_public(): void
    {
        $ref = new \ReflectionMethod(OssController::class, 'generateFilename');

        $this->assertTrue($ref->isPublic());
    }

    public function test_private_image_proxy_is_public(): void
    {
        $ref = new \ReflectionMethod(OssController::class, 'privateImageProxy');

        $this->assertTrue($ref->isPublic());
    }

    public function test_get_sts_token_accepts_request_parameter(): void
    {
        $ref = new \ReflectionMethod(OssController::class, 'getStsToken');
        $params = $ref->getParameters();

        $this->assertCount(1, $params);
        $this->assertEquals('request', $params[0]->getName());
    }

    public function test_get_sts_token_returns_json_response(): void
    {
        $ref = new \ReflectionMethod(OssController::class, 'getStsToken');
        $returnType = $ref->getReturnType();

        $this->assertNotNull($returnType);
        $this->assertEquals('Illuminate\Http\JsonResponse', $returnType->getName());
    }

    public function test_private_image_proxy_accepts_path_parameter(): void
    {
        $ref = new \ReflectionMethod(OssController::class, 'privateImageProxy');
        $params = $ref->getParameters();

        $this->assertCount(1, $params);
        $this->assertEquals('path', $params[0]->getName());
    }
}
