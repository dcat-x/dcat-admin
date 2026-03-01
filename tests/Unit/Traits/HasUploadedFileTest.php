<?php

namespace Dcat\Admin\Tests\Unit\Traits;

use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Traits\HasUploadedFile;
use Mockery;

class HasUploadedFileTestHelper
{
    use HasUploadedFile;
}

class HasUploadedFileTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_trait_exists(): void
    {
        $this->assertTrue(trait_exists(HasUploadedFile::class));
    }

    public function test_trait_has_uploader_method(): void
    {
        $this->assertTrue(method_exists(HasUploadedFileTestHelper::class, 'uploader'));
    }

    public function test_trait_has_file_method(): void
    {
        $this->assertTrue(method_exists(HasUploadedFileTestHelper::class, 'file'));
    }

    public function test_trait_has_disk_method(): void
    {
        $this->assertTrue(method_exists(HasUploadedFileTestHelper::class, 'disk'));
    }

    public function test_trait_has_is_delete_request_method(): void
    {
        $this->assertTrue(method_exists(HasUploadedFileTestHelper::class, 'isDeleteRequest'));
    }

    public function test_trait_has_delete_file_method(): void
    {
        $this->assertTrue(method_exists(HasUploadedFileTestHelper::class, 'deleteFile'));
    }

    public function test_trait_has_response_deleted_method(): void
    {
        $this->assertTrue(method_exists(HasUploadedFileTestHelper::class, 'responseDeleted'));
    }

    public function test_trait_has_response_error_message_method(): void
    {
        $this->assertTrue(method_exists(HasUploadedFileTestHelper::class, 'responseErrorMessage'));
    }

    public function test_trait_has_delete_file_and_response_method(): void
    {
        $this->assertTrue(method_exists(HasUploadedFileTestHelper::class, 'deleteFileAndResponse'));
    }

    public function test_trait_has_response_uploaded_method(): void
    {
        $this->assertTrue(method_exists(HasUploadedFileTestHelper::class, 'responseUploaded'));
    }

    public function test_trait_has_response_validation_message_method(): void
    {
        $this->assertTrue(method_exists(HasUploadedFileTestHelper::class, 'responseValidationMessage'));
    }

    public function test_disk_method_accepts_nullable_string(): void
    {
        $reflection = new \ReflectionMethod(HasUploadedFileTestHelper::class, 'disk');
        $params = $reflection->getParameters();

        $this->assertCount(1, $params);
        $this->assertTrue($params[0]->allowsNull());
        $this->assertTrue($params[0]->isDefaultValueAvailable());
        $this->assertNull($params[0]->getDefaultValue());
    }

    public function test_is_delete_request_returns_bool(): void
    {
        $helper = new HasUploadedFileTestHelper;

        $result = $helper->isDeleteRequest();

        $this->assertIsBool($result);
        $this->assertFalse($result);
    }
}
