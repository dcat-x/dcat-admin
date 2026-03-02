<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\File;
use Dcat\Admin\Form\Field\OssDirectUpload;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class OssDirectUploadTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    // -------------------------------------------------------
    // Class existence and inheritance
    // -------------------------------------------------------

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(OssDirectUpload::class));
    }

    public function test_extends_file(): void
    {
        $this->assertTrue(is_subclass_of(OssDirectUpload::class, File::class));
    }

    // -------------------------------------------------------
    // Default property values via reflection
    // -------------------------------------------------------

    public function test_view_is_admin_form_oss_direct_upload(): void
    {
        $reflection = new \ReflectionClass(OssDirectUpload::class);
        $property = $reflection->getProperty('view');
        $property->setAccessible(true);

        $defaults = $reflection->getDefaultProperties();
        $this->assertSame('admin::form.oss-direct-upload', $defaults['view']);
    }

    public function test_max_size_mb_default_is_500(): void
    {
        $reflection = new \ReflectionClass(OssDirectUpload::class);
        $defaults = $reflection->getDefaultProperties();

        $this->assertArrayHasKey('maxSizeMb', $defaults);
        $this->assertSame(500, $defaults['maxSizeMb']);
    }

    public function test_chunk_size_mb_default_is_10(): void
    {
        $reflection = new \ReflectionClass(OssDirectUpload::class);
        $defaults = $reflection->getDefaultProperties();

        $this->assertArrayHasKey('chunkSizeMb', $defaults);
        $this->assertSame(10, $defaults['chunkSizeMb']);
    }

    public function test_upload_type_default_is_file(): void
    {
        $reflection = new \ReflectionClass(OssDirectUpload::class);
        $defaults = $reflection->getDefaultProperties();

        $this->assertArrayHasKey('uploadType', $defaults);
        $this->assertSame('file', $defaults['uploadType']);
    }

    public function test_accept_extensions_default_is_wildcard(): void
    {
        $reflection = new \ReflectionClass(OssDirectUpload::class);
        $defaults = $reflection->getDefaultProperties();

        $this->assertArrayHasKey('acceptExtensions', $defaults);
        $this->assertSame('*', $defaults['acceptExtensions']);
    }

    public function test_upload_directory_default_is_null(): void
    {
        $reflection = new \ReflectionClass(OssDirectUpload::class);
        $defaults = $reflection->getDefaultProperties();

        $this->assertArrayHasKey('uploadDirectory', $defaults);
        $this->assertNull($defaults['uploadDirectory']);
    }

    public function test_accept_mime_types_default_is_null(): void
    {
        $reflection = new \ReflectionClass(OssDirectUpload::class);
        $defaults = $reflection->getDefaultProperties();

        $this->assertArrayHasKey('acceptMimeTypes', $defaults);
        $this->assertNull($defaults['acceptMimeTypes']);
    }

    public function test_sts_token_url_default_is_null(): void
    {
        $reflection = new \ReflectionClass(OssDirectUpload::class);
        $defaults = $reflection->getDefaultProperties();

        $this->assertArrayHasKey('stsTokenUrl', $defaults);
        $this->assertNull($defaults['stsTokenUrl']);
    }

    // -------------------------------------------------------
    // Method existence
    // -------------------------------------------------------

    public function test_max_size_method_exists(): void
    {
        $this->assertTrue(method_exists(OssDirectUpload::class, 'maxSize'));
    }

    public function test_chunk_size_method_exists(): void
    {
        $this->assertTrue(method_exists(OssDirectUpload::class, 'chunkSize'));
    }

    public function test_upload_type_method_exists(): void
    {
        $this->assertTrue(method_exists(OssDirectUpload::class, 'uploadType'));
    }

    public function test_directory_method_exists(): void
    {
        $this->assertTrue(method_exists(OssDirectUpload::class, 'directory'));
    }

    public function test_accept_method_exists(): void
    {
        $this->assertTrue(method_exists(OssDirectUpload::class, 'accept'));
    }

    public function test_sts_token_url_method_exists(): void
    {
        $this->assertTrue(method_exists(OssDirectUpload::class, 'stsTokenUrl'));
    }

    public function test_render_method_exists(): void
    {
        $this->assertTrue(method_exists(OssDirectUpload::class, 'render'));
    }

    // -------------------------------------------------------
    // Method visibility
    // -------------------------------------------------------

    public function test_max_size_is_public(): void
    {
        $method = new \ReflectionMethod(OssDirectUpload::class, 'maxSize');
        $this->assertTrue($method->isPublic());
    }

    public function test_chunk_size_is_public(): void
    {
        $method = new \ReflectionMethod(OssDirectUpload::class, 'chunkSize');
        $this->assertTrue($method->isPublic());
    }

    public function test_upload_type_is_public(): void
    {
        $method = new \ReflectionMethod(OssDirectUpload::class, 'uploadType');
        $this->assertTrue($method->isPublic());
    }

    public function test_directory_is_public(): void
    {
        $method = new \ReflectionMethod(OssDirectUpload::class, 'directory');
        $this->assertTrue($method->isPublic());
    }

    public function test_accept_is_public(): void
    {
        $method = new \ReflectionMethod(OssDirectUpload::class, 'accept');
        $this->assertTrue($method->isPublic());
    }

    public function test_sts_token_url_is_public(): void
    {
        $method = new \ReflectionMethod(OssDirectUpload::class, 'stsTokenUrl');
        $this->assertTrue($method->isPublic());
    }

    public function test_render_is_public(): void
    {
        $method = new \ReflectionMethod(OssDirectUpload::class, 'render');
        $this->assertTrue($method->isPublic());
    }

    // -------------------------------------------------------
    // Return type checks
    // -------------------------------------------------------

    public function test_max_size_return_type_is_static(): void
    {
        $method = new \ReflectionMethod(OssDirectUpload::class, 'maxSize');
        $returnType = $method->getReturnType();

        $this->assertNotNull($returnType);
        $this->assertSame('static', $returnType->getName());
    }

    public function test_chunk_size_return_type_is_static(): void
    {
        $method = new \ReflectionMethod(OssDirectUpload::class, 'chunkSize');
        $returnType = $method->getReturnType();

        $this->assertNotNull($returnType);
        $this->assertSame('static', $returnType->getName());
    }

    public function test_upload_type_return_type_is_static(): void
    {
        $method = new \ReflectionMethod(OssDirectUpload::class, 'uploadType');
        $returnType = $method->getReturnType();

        $this->assertNotNull($returnType);
        $this->assertSame('static', $returnType->getName());
    }

    public function test_directory_return_type_is_static(): void
    {
        $method = new \ReflectionMethod(OssDirectUpload::class, 'directory');
        $returnType = $method->getReturnType();

        $this->assertNotNull($returnType);
        $this->assertSame('static', $returnType->getName());
    }

    public function test_sts_token_url_return_type_is_static(): void
    {
        $method = new \ReflectionMethod(OssDirectUpload::class, 'stsTokenUrl');
        $returnType = $method->getReturnType();

        $this->assertNotNull($returnType);
        $this->assertSame('static', $returnType->getName());
    }
}
