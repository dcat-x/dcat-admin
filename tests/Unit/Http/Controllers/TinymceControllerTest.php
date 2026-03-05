<?php

namespace Dcat\Admin\Tests\Unit\Http\Controllers;

use Dcat\Admin\Http\Controllers\TinymceController;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class TinymceControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_upload_stores_file_and_returns_expected_location_payload(): void
    {
        $file = $this->makeUploadedFile('doc-image.png', 'tinymce-content');
        $disk = Mockery::mock(FilesystemAdapter::class);

        $disk->shouldReceive('putFileAs')->once()->with('docs', Mockery::type(\Illuminate\Http\UploadedFile::class), 'fixed-name.png');
        $disk->shouldReceive('url')->once()->with('docs/fixed-name.png')->andReturn('https://cdn.example.com/docs/fixed-name.png');

        $controller = new class($disk) extends TinymceController
        {
            public function __construct(private FilesystemAdapter $mockDisk) {}

            protected function disk(): FilesystemAdapter
            {
                return $this->mockDisk;
            }

            protected function generateNewName(UploadedFile $file)
            {
                return 'fixed-name.png';
            }
        };

        $request = Request::create('/tinymce/upload', 'POST', ['dir' => '/docs/']);
        $request->files->set('file', $file);

        $result = $controller->upload($request);

        $this->assertSame('https://cdn.example.com/docs/fixed-name.png', $result['location']);

        @unlink($file->getPathname());
    }

    public function test_generate_new_name_uses_original_name_hash_and_extension(): void
    {
        $file = Mockery::mock(UploadedFile::class);
        $file->shouldReceive('getClientOriginalName')->andReturn('manual.pdf');
        $file->shouldReceive('getClientOriginalExtension')->andReturn('pdf');

        $controller = new class extends TinymceController
        {
            public function exposeGenerateNewName(UploadedFile $file)
            {
                return $this->generateNewName($file);
            }
        };

        $generated = $controller->exposeGenerateNewName($file);

        $this->assertStringEndsWith('.pdf', $generated);
        $this->assertStringStartsWith(md5('manual.pdf'), $generated);
    }

    public function test_disk_resolves_from_request_disk_parameter(): void
    {
        $adapter = Mockery::mock(FilesystemAdapter::class);
        Storage::shouldReceive('disk')->once()->with('oss-public')->andReturn($adapter);

        $request = Request::create('/tinymce/upload', 'POST', ['disk' => 'oss-public']);
        $this->app->instance('request', $request);

        $controller = new class extends TinymceController
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
