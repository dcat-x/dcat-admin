<?php

namespace Dcat\Admin\Tests\Unit\Support;

use Dcat\Admin\Support\WebUploader;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Http\Request;
use Mockery;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class WebUploaderTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function createUploaderWithRequest(array $params = [], ?UploadedFile $file = null): WebUploader
    {
        $request = Request::create('/upload', 'POST', $params);

        if ($file) {
            $request->files->set(WebUploader::FILE_NAME, $file);
        }

        return new WebUploader($request);
    }

    public function test_file_name_constant(): void
    {
        $this->assertSame('_file_', WebUploader::FILE_NAME);
    }

    public function test_constructor_sets_properties_from_request(): void
    {
        $uploader = $this->createUploaderWithRequest([
            '_id' => 'upload-123',
            'chunk' => 0,
            'chunks' => 3,
            'upload_column' => 'avatar',
        ]);

        $this->assertSame('upload-123', $uploader->_id);
        $this->assertEquals(0, $uploader->chunk);
        $this->assertEquals(3, $uploader->chunks);
        $this->assertSame('avatar', $uploader->upload_column);
    }

    public function test_has_chunk_file_returns_true_when_multiple_chunks(): void
    {
        $uploader = $this->createUploaderWithRequest([
            'chunks' => 3,
        ]);

        $this->assertTrue($uploader->hasChunkFile());
    }

    public function test_has_chunk_file_returns_false_when_single_chunk(): void
    {
        $uploader = $this->createUploaderWithRequest([
            'chunks' => 1,
        ]);

        $this->assertFalse($uploader->hasChunkFile());
    }

    public function test_has_chunk_file_returns_false_when_no_chunks(): void
    {
        $uploader = $this->createUploaderWithRequest([]);

        $this->assertFalse($uploader->hasChunkFile());
    }

    public function test_is_uploading_returns_false_without_file(): void
    {
        $uploader = $this->createUploaderWithRequest([
            'upload_column' => 'avatar',
        ]);

        $this->assertFalse($uploader->isUploading());
    }

    public function test_is_uploading_returns_false_without_upload_column(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents($tmpFile, 'test content');

        $file = new UploadedFile($tmpFile, 'test.jpg', 'image/jpeg', null, true);
        $uploader = $this->createUploaderWithRequest([], $file);

        $this->assertFalse($uploader->isUploading());

        @unlink($tmpFile);
    }

    public function test_is_uploading_returns_true_with_file_and_column(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents($tmpFile, 'test content');

        $file = new UploadedFile($tmpFile, 'test.jpg', 'image/jpeg', null, true);
        $uploader = $this->createUploaderWithRequest([
            'upload_column' => 'avatar',
        ], $file);

        $this->assertTrue($uploader->isUploading());

        @unlink($tmpFile);
    }

    public function test_get_uploaded_file_returns_null_without_file(): void
    {
        $uploader = $this->createUploaderWithRequest([]);

        $this->assertNull($uploader->getUploadedFile());
    }

    public function test_get_uploaded_file_returns_file_for_single_chunk(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents($tmpFile, 'test content');

        $file = new UploadedFile($tmpFile, 'test.jpg', 'image/jpeg', null, true);
        $uploader = $this->createUploaderWithRequest([
            'chunks' => 1,
        ], $file);

        $result = $uploader->getUploadedFile();

        $this->assertInstanceOf(UploadedFile::class, $result);

        @unlink($tmpFile);
    }

    public function test_temporary_directory_default(): void
    {
        $uploader = $this->createUploaderWithRequest([]);

        $this->assertSame('tmp', $uploader->temporaryDirectory);
    }

    public function test_get_temporary_path_appends_to_directory(): void
    {
        $uploader = $this->createUploaderWithRequest([]);

        $path = $uploader->getTemporaryPath('upload-123');

        $this->assertStringEndsWith('/upload-123', $path);
        $this->assertStringContainsString('tmp', $path);
    }

    public function test_delete_temporary_file_does_nothing_without_path(): void
    {
        $uploader = $this->createUploaderWithRequest([]);

        // Should not throw any errors
        $uploader->deleteTemporaryFile();

        $this->assertTrue(true);
    }

    public function test_prepare_request_trims_relation(): void
    {
        $request = Request::create('/upload', 'POST', [
            '_relation' => ',some_relation,',
        ]);

        $uploader = new WebUploader($request);

        // The prepareRequest method should trim commas from _relation
        $ref = new \ReflectionMethod(WebUploader::class, 'prepareRequest');
        $ref->setAccessible(true);

        $prepared = $ref->invoke($uploader, $request);

        $this->assertSame('some_relation', $prepared->get('_relation'));
    }

    public function test_generate_chunk_file_name_returns_md5(): void
    {
        $uploader = $this->createUploaderWithRequest([]);

        $tmpFile = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents($tmpFile, 'test content');

        $file = new UploadedFile($tmpFile, 'my_photo.jpg', 'image/jpeg', null, true);

        $ref = new \ReflectionMethod(WebUploader::class, 'generateChunkFileName');
        $ref->setAccessible(true);

        $result = $ref->invoke($uploader, $file);

        $this->assertSame(md5('my_photo.jpg'), $result);

        @unlink($tmpFile);
    }
}
