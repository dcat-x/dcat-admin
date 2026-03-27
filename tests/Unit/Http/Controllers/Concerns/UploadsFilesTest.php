<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Http\Controllers\Concerns;

use Dcat\Admin\Http\Controllers\Concerns\UploadsFiles;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Mockery;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadsFilesTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_validate_upload_file_throws_when_missing(): void
    {
        $controller = $this->makeController();
        $request = Request::create('/upload', 'POST');
        $request->setLaravelSession($this->app['session.store']);

        $this->expectException(ValidationException::class);
        $controller->callValidateUploadFile($request, 'file');
    }

    public function test_validate_upload_file_returns_file_when_present(): void
    {
        $controller = $this->makeController();
        $tmpFile = $this->makeTempFile('test.jpg', 'content');
        $request = Request::create('/upload', 'POST');
        $request->files->set('file', $tmpFile);

        $result = $controller->callValidateUploadFile($request, 'file');

        $this->assertInstanceOf(UploadedFile::class, $result);
        @unlink($tmpFile->getPathname());
    }

    public function test_generate_new_name_uses_random_string_and_extension(): void
    {
        $controller = $this->makeController();
        $file = Mockery::mock(UploadedFile::class);
        $file->shouldReceive('getClientOriginalExtension')->andReturn('png');

        $name = $controller->callGenerateNewName($file);

        $this->assertStringEndsWith('.png', $name);
        $this->assertSame(36, strlen($name)); // 32 random + 1 dot + 3 ext
    }

    public function test_generate_new_name_produces_unique_names(): void
    {
        $controller = $this->makeController();
        $file = Mockery::mock(UploadedFile::class);
        $file->shouldReceive('getClientOriginalExtension')->andReturn('jpg');

        $name1 = $controller->callGenerateNewName($file);
        $name2 = $controller->callGenerateNewName($file);

        $this->assertNotSame($name1, $name2);
    }

    public function test_disk_resolves_from_request_parameter(): void
    {
        $adapter = Mockery::mock(FilesystemAdapter::class);
        Storage::shouldReceive('disk')->once()->with('custom-disk')->andReturn($adapter);

        $request = Request::create('/upload', 'POST', ['disk' => 'custom-disk']);
        $this->app->instance('request', $request);

        $controller = $this->makeController();

        $this->assertSame($adapter, $controller->callDisk());
    }

    public function test_disk_falls_back_to_config(): void
    {
        $adapter = Mockery::mock(FilesystemAdapter::class);
        $this->app['config']->set('admin.upload.disk', 'local');
        Storage::shouldReceive('disk')->once()->with('local')->andReturn($adapter);

        $request = Request::create('/upload', 'POST');
        $this->app->instance('request', $request);

        $controller = $this->makeController();

        $this->assertSame($adapter, $controller->callDisk());
    }

    protected function makeController(): object
    {
        return new class
        {
            use UploadsFiles;

            public function callValidateUploadFile(Request $request, string $field): UploadedFile
            {
                return $this->validateUploadFile($request, $field);
            }

            public function callGenerateNewName(UploadedFile $file): string
            {
                return $this->generateNewName($file);
            }

            public function callDisk()
            {
                return $this->disk();
            }
        };
    }

    protected function makeTempFile(string $name, string $content): \Illuminate\Http\UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'dcat_test_');
        file_put_contents($path, $content);

        return new \Illuminate\Http\UploadedFile($path, $name, null, null, true);
    }
}
