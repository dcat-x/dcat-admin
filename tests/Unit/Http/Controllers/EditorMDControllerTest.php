<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Http\Controllers;

use Dcat\Admin\Http\Controllers\EditorMDController;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class EditorMDControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_upload_stores_file_and_returns_expected_url_payload(): void
    {
        $file = $this->makeUploadedFile('editor-image.jpg', 'editor-content');
        $disk = Mockery::mock(FilesystemAdapter::class);

        $disk->shouldReceive('putFileAs')->once()->with('articles', Mockery::type(UploadedFile::class), 'fixed-name.jpg');
        $disk->shouldReceive('url')->once()->with('articles/fixed-name.jpg')->andReturn('https://cdn.example.com/articles/fixed-name.jpg');

        $controller = new class($disk, $file) extends EditorMDController
        {
            public function __construct(
                private FilesystemAdapter $mockDisk,
                private UploadedFile $mockFile,
            ) {}

            protected function validateUploadFile(Request $request, string $fieldName): UploadedFile
            {
                return $this->mockFile;
            }

            protected function disk(): FilesystemAdapter
            {
                return $this->mockDisk;
            }

            protected function generateNewName(UploadedFile $file): string
            {
                return 'fixed-name.jpg';
            }
        };

        $request = Request::create('/editor/upload', 'POST', ['dir' => '/articles/']);

        $result = $controller->upload($request);

        $this->assertSame(1, $result['success']);
        $this->assertSame('https://cdn.example.com/articles/fixed-name.jpg', $result['url']);

        @unlink($file->getPathname());
    }

    public function test_generate_new_name_uses_random_string_and_extension(): void
    {
        $file = Mockery::mock(UploadedFile::class);
        $file->shouldReceive('getClientOriginalExtension')->andReturn('png');

        $controller = new class extends EditorMDController
        {
            public function exposeGenerateNewName(UploadedFile $file): string
            {
                return $this->generateNewName($file);
            }
        };

        $generated = $controller->exposeGenerateNewName($file);

        $this->assertStringEndsWith('.png', $generated);
        $this->assertSame(36, strlen($generated));
    }

    public function test_disk_resolves_from_request_disk_parameter(): void
    {
        $adapter = Mockery::mock(FilesystemAdapter::class);
        Storage::shouldReceive('disk')->once()->with('oss-private')->andReturn($adapter);

        $request = Request::create('/editor/upload', 'POST', ['disk' => 'oss-private']);
        $this->app->instance('request', $request);

        $controller = new class extends EditorMDController
        {
            public function exposeDisk(): FilesystemAdapter
            {
                return $this->disk();
            }
        };

        $this->assertSame($adapter, $controller->exposeDisk());
    }

    protected function makeUploadedFile(string $name, string $content): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'dcat_test_');
        file_put_contents($path, $content);

        return new UploadedFile($path, $name, null, null, true);
    }
}
