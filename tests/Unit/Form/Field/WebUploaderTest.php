<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\WebUploader;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class WebUploaderTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    // -------------------------------------------------------
    // Trait existence
    // -------------------------------------------------------

    public function test_trait_exists(): void
    {
        $this->assertTrue(trait_exists(WebUploader::class));
    }

    // -------------------------------------------------------
    // Method existence
    // -------------------------------------------------------

    public function test_accept_method_exists(): void
    {
        $this->assertTrue(method_exists(WebUploader::class, 'accept'));
    }

    public function test_mime_types_method_exists(): void
    {
        $this->assertTrue(method_exists(WebUploader::class, 'mimeTypes'));
    }

    public function test_chunked_method_exists(): void
    {
        $this->assertTrue(method_exists(WebUploader::class, 'chunked'));
    }

    public function test_chunk_size_method_exists(): void
    {
        $this->assertTrue(method_exists(WebUploader::class, 'chunkSize'));
    }

    public function test_auto_upload_method_exists(): void
    {
        $this->assertTrue(method_exists(WebUploader::class, 'autoUpload'));
    }

    public function test_threads_method_exists(): void
    {
        $this->assertTrue(method_exists(WebUploader::class, 'threads'));
    }

    public function test_max_size_method_exists(): void
    {
        $this->assertTrue(method_exists(WebUploader::class, 'maxSize'));
    }

    public function test_removable_method_exists(): void
    {
        $this->assertTrue(method_exists(WebUploader::class, 'removable'));
    }

    public function test_downloadable_method_exists(): void
    {
        $this->assertTrue(method_exists(WebUploader::class, 'downloadable'));
    }

    public function test_compress_method_exists(): void
    {
        $this->assertTrue(method_exists(WebUploader::class, 'compress'));
    }

    public function test_url_method_exists(): void
    {
        $this->assertTrue(method_exists(WebUploader::class, 'url'));
    }

    public function test_auto_save_method_exists(): void
    {
        $this->assertTrue(method_exists(WebUploader::class, 'autoSave'));
    }

    public function test_delete_url_method_exists(): void
    {
        $this->assertTrue(method_exists(WebUploader::class, 'deleteUrl'));
    }

    public function test_with_form_data_method_exists(): void
    {
        $this->assertTrue(method_exists(WebUploader::class, 'withFormData'));
    }

    public function test_with_delete_data_method_exists(): void
    {
        $this->assertTrue(method_exists(WebUploader::class, 'withDeleteData'));
    }

    public function test_get_create_url_method_exists(): void
    {
        $this->assertTrue(method_exists(WebUploader::class, 'getCreateUrl'));
    }

    // -------------------------------------------------------
    // Method visibility checks
    // -------------------------------------------------------

    public function test_accept_is_public(): void
    {
        $method = new \ReflectionMethod(WebUploader::class, 'accept');
        $this->assertTrue($method->isPublic());
    }

    public function test_mime_types_is_public(): void
    {
        $method = new \ReflectionMethod(WebUploader::class, 'mimeTypes');
        $this->assertTrue($method->isPublic());
    }

    public function test_chunked_is_public(): void
    {
        $method = new \ReflectionMethod(WebUploader::class, 'chunked');
        $this->assertTrue($method->isPublic());
    }

    public function test_chunk_size_is_public(): void
    {
        $method = new \ReflectionMethod(WebUploader::class, 'chunkSize');
        $this->assertTrue($method->isPublic());
    }

    public function test_auto_upload_is_public(): void
    {
        $method = new \ReflectionMethod(WebUploader::class, 'autoUpload');
        $this->assertTrue($method->isPublic());
    }

    public function test_threads_is_public(): void
    {
        $method = new \ReflectionMethod(WebUploader::class, 'threads');
        $this->assertTrue($method->isPublic());
    }

    public function test_max_size_is_public(): void
    {
        $method = new \ReflectionMethod(WebUploader::class, 'maxSize');
        $this->assertTrue($method->isPublic());
    }

    public function test_removable_is_public(): void
    {
        $method = new \ReflectionMethod(WebUploader::class, 'removable');
        $this->assertTrue($method->isPublic());
    }

    public function test_downloadable_is_public(): void
    {
        $method = new \ReflectionMethod(WebUploader::class, 'downloadable');
        $this->assertTrue($method->isPublic());
    }

    // -------------------------------------------------------
    // Protected methods
    // -------------------------------------------------------

    public function test_set_up_default_options_is_protected(): void
    {
        $method = new \ReflectionMethod(WebUploader::class, 'setUpDefaultOptions');
        $this->assertTrue($method->isProtected());
    }

    public function test_set_default_server_is_protected(): void
    {
        $method = new \ReflectionMethod(WebUploader::class, 'setDefaultServer');
        $this->assertTrue($method->isProtected());
    }

    public function test_setup_preview_options_is_protected(): void
    {
        $method = new \ReflectionMethod(WebUploader::class, 'setupPreviewOptions');
        $this->assertTrue($method->isProtected());
    }

    // -------------------------------------------------------
    // Parameter checks
    // -------------------------------------------------------

    public function test_accept_has_two_parameters(): void
    {
        $method = new \ReflectionMethod(WebUploader::class, 'accept');
        $params = $method->getParameters();

        $this->assertCount(2, $params);
        $this->assertSame('extensions', $params[0]->getName());
        $this->assertSame('mimeTypes', $params[1]->getName());
    }

    public function test_accept_mime_types_parameter_is_nullable(): void
    {
        $method = new \ReflectionMethod(WebUploader::class, 'accept');
        $params = $method->getParameters();

        $this->assertTrue($params[1]->allowsNull());
    }

    public function test_chunked_has_bool_parameter_with_default_true(): void
    {
        $method = new \ReflectionMethod(WebUploader::class, 'chunked');
        $params = $method->getParameters();

        $this->assertCount(1, $params);
        $this->assertSame('value', $params[0]->getName());
        $this->assertTrue($params[0]->getDefaultValue());
    }

    public function test_chunk_size_has_int_parameter(): void
    {
        $method = new \ReflectionMethod(WebUploader::class, 'chunkSize');
        $params = $method->getParameters();

        $this->assertCount(1, $params);
        $this->assertSame('size', $params[0]->getName());
    }

    public function test_threads_has_int_parameter(): void
    {
        $method = new \ReflectionMethod(WebUploader::class, 'threads');
        $params = $method->getParameters();

        $this->assertCount(1, $params);
        $this->assertSame('num', $params[0]->getName());
    }
}
