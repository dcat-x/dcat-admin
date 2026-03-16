<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Http\Controllers;

use Carbon\Carbon;
use Dcat\Admin\Http\Controllers\OssController;
use Dcat\Admin\Services\AliyunStsService;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Mockery;

class OssControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::create(2026, 3, 5, 10, 0, 0));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        Mockery::close();
        parent::tearDown();
    }

    public function test_generate_upload_dir_by_type(): void
    {
        $controller = new class extends OssController
        {
            public function exposeGenerateUploadDir(string $type): string
            {
                return $this->generateUploadDir($type);
            }
        };

        $this->assertSame('images/2026/03/05/', $controller->exposeGenerateUploadDir('image'));
        $this->assertSame('files/2026/03/05/', $controller->exposeGenerateUploadDir('file'));
        $this->assertSame('files/2026/03/05/', $controller->exposeGenerateUploadDir('unknown'));
    }

    public function test_validate_and_format_directory_accepts_whitelisted_path(): void
    {
        $this->app['config']->set('admin.upload.oss.allowed_directories', ['images', 'files']);

        $controller = new class extends OssController
        {
            public function exposeValidateDirectory(string $directory): string
            {
                return $this->validateAndFormatDirectory($directory);
            }
        };

        $this->assertSame('images/avatar', $controller->exposeValidateDirectory('/images/avatar/'));
    }

    public function test_validate_and_format_directory_rejects_path_traversal(): void
    {
        $controller = new class extends OssController
        {
            public function exposeValidateDirectory(string $directory): string
            {
                return $this->validateAndFormatDirectory($directory);
            }
        };

        $this->expectException(\Exception::class);
        $controller->exposeValidateDirectory('../secret');
    }

    public function test_generate_filename_returns_path_with_extension_and_date_prefix(): void
    {
        $controller = new OssController;
        $request = Request::create('/oss/filename', 'GET', ['type' => 'image', 'extension' => 'jpg']);

        $response = $controller->generateFilename($request);
        $data = $response->getData(true);

        $this->assertTrue($data['success']);
        $this->assertStringEndsWith('.jpg', $data['data']['filename']);
        $this->assertStringStartsWith('images/2026/03/05/', $data['data']['path']);
        $this->assertStringContainsString($data['data']['filename'], $data['data']['path']);
    }

    public function test_private_image_proxy_returns_400_for_empty_path(): void
    {
        $controller = new OssController;

        $response = $controller->privateImageProxy('');

        $this->assertSame(400, $response->status());
        $this->assertSame(['error' => 'Path is required'], $response->getData(true));
    }

    public function test_private_image_proxy_returns_400_for_invalid_path(): void
    {
        $controller = new OssController;

        $response = $controller->privateImageProxy('../etc/passwd');

        $this->assertSame(400, $response->status());
        $this->assertSame(['error' => 'Invalid path'], $response->getData(true));
    }

    public function test_private_image_proxy_redirects_to_signed_url_when_disk_supports_temporary_url(): void
    {
        $disk = new class
        {
            public function temporaryUrl(string $path): string
            {
                return 'https://signed.example.com/'.$path;
            }
        };

        Storage::shouldReceive('disk')->once()->andReturn($disk);

        $controller = new OssController;
        $response = $controller->privateImageProxy('images/a.jpg');

        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('https://signed.example.com/images/a.jpg', $response->getTargetUrl());
    }

    public function test_get_sts_token_returns_success_payload(): void
    {
        $service = Mockery::mock(AliyunStsService::class);
        $service->shouldReceive('getStsToken')->once()->with('images/2026/03/05/')->andReturn([
            'accessKeyId' => 'id',
            'accessKeySecret' => 'secret',
            'securityToken' => 'token',
            'expiration' => '2026-03-05T12:00:00Z',
        ]);
        $service->shouldReceive('getOssConfig')->once()->andReturn([
            'region' => 'cn-hangzhou',
            'bucket' => 'test-bucket',
            'endpoint' => 'oss-cn-hangzhou.aliyuncs.com',
            'cdn_domain' => null,
        ]);

        $this->app->instance(AliyunStsService::class, $service);

        $controller = new OssController;
        $request = Request::create('/oss/sts', 'GET', ['type' => 'image']);

        $response = $controller->getStsToken($request);
        $data = $response->getData(true);

        $this->assertTrue($data['success']);
        $this->assertSame('images/2026/03/05/', $data['data']['upload_dir']);
        $this->assertSame('test-bucket', $data['data']['bucket']);
    }
}
