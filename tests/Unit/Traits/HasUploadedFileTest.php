<?php

declare(strict_types=1);

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

    public function test_uploader_and_file_delegate_to_uploader_service(): void
    {
        $uploaded = (object) ['name' => 'avatar.png'];
        $uploader = new class($uploaded)
        {
            public function __construct(private object $uploaded) {}

            public function getUploadedFile(): object
            {
                return $this->uploaded;
            }
        };

        $this->app->instance('admin.web-uploader', $uploader);

        $helper = new HasUploadedFileTestHelper;

        $this->assertSame($uploader, $helper->uploader());
        $this->assertSame($uploaded, $helper->file());
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

    public function test_disk_resolves_configured_and_explicit_disks(): void
    {
        $this->app['config']->set('admin.upload.disk', 'local');
        $helper = new HasUploadedFileTestHelper;

        $this->assertInstanceOf(\Illuminate\Filesystem\FilesystemAdapter::class, $helper->disk());
        $this->assertInstanceOf(\Illuminate\Filesystem\FilesystemAdapter::class, $helper->disk('local'));
    }

    public function test_is_delete_request_detects_request_flag(): void
    {
        $helper = new HasUploadedFileTestHelper;

        $this->assertFalse($helper->isDeleteRequest());

        $request = \Illuminate\Http\Request::create('/upload/delete', 'POST', [
            \Dcat\Admin\Form\Field\File::FILE_DELETE_FLAG => 1,
        ]);
        $this->app->instance('request', $request);

        $result = $helper->isDeleteRequest();

        $this->assertIsBool($result);
        $this->assertTrue($result);
    }

    public function test_delete_file_uses_given_disk_and_path(): void
    {
        $helper = new HasUploadedFileTestHelper;

        $disk = Mockery::mock();
        $disk->shouldReceive('delete')->once()->with('avatars/a.png')->andReturn(true);

        $this->assertTrue($helper->deleteFile($disk, 'avatars/a.png'));
    }

    public function test_delete_file_and_response_returns_success_json(): void
    {
        $helper = new HasUploadedFileTestHelper;

        $disk = Mockery::mock();
        $disk->shouldReceive('delete')->once()->with('avatars/a.png')->andReturn(true);

        $response = $helper->deleteFileAndResponse($disk, 'avatars/a.png');

        $this->assertTrue($response->toArray()['status']);
        $this->assertSame([], $response->toArray()['data']);
    }

    public function test_response_uploaded_contains_file_info(): void
    {
        $helper = new HasUploadedFileTestHelper;

        $response = $helper->responseUploaded('avatars/a.png', 'http://localhost/storage/avatars/a.png');
        $payload = $response->toArray();

        $this->assertTrue($payload['status']);
        $this->assertSame('avatars/a.png', $payload['data']['id']);
        $this->assertSame('a.png', $payload['data']['name']);
        $this->assertSame('a.png', $payload['data']['path']);
        $this->assertSame('http://localhost/storage/avatars/a.png', $payload['data']['url']);
    }

    public function test_response_validation_message_returns_error_json(): void
    {
        $helper = new HasUploadedFileTestHelper;

        $response = $helper->responseValidationMessage('invalid file');
        $payload = $response->toArray();

        $this->assertFalse($payload['status']);
        $this->assertSame('invalid file', $payload['data']['message']);
    }
}
