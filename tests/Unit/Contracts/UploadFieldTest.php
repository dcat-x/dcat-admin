<?php

namespace Dcat\Admin\Tests\Unit\Contracts;

use Dcat\Admin\Contracts\UploadField;
use Dcat\Admin\Tests\TestCase;
use Mockery;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadFieldTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_interface_exists(): void
    {
        $this->assertTrue(interface_exists(UploadField::class));
    }

    public function test_has_upload_method(): void
    {
        $reflection = new \ReflectionClass(UploadField::class);

        $this->assertTrue($reflection->hasMethod('upload'));

        $params = $reflection->getMethod('upload')->getParameters();
        $this->assertCount(1, $params);
        $this->assertSame('file', $params[0]->getName());
    }

    public function test_upload_method_requires_uploaded_file_parameter(): void
    {
        $reflection = new \ReflectionMethod(UploadField::class, 'upload');
        $params = $reflection->getParameters();

        $this->assertSame(UploadedFile::class, $params[0]->getType()->getName());
    }

    public function test_has_destroy_method(): void
    {
        $reflection = new \ReflectionClass(UploadField::class);

        $this->assertTrue($reflection->hasMethod('destroy'));
        $this->assertCount(0, $reflection->getMethod('destroy')->getParameters());
    }

    public function test_has_delete_file_method(): void
    {
        $reflection = new \ReflectionClass(UploadField::class);

        $this->assertTrue($reflection->hasMethod('deleteFile'));

        $params = $reflection->getMethod('deleteFile')->getParameters();
        $this->assertCount(1, $params);
        $this->assertSame('path', $params[0]->getName());
    }

    public function test_implementation_is_instance_of_upload_field(): void
    {
        $instance = new class implements UploadField
        {
            public function upload(UploadedFile $file)
            {
                return null;
            }

            public function destroy() {}

            public function deleteFile($path) {}
        };

        $this->assertInstanceOf(UploadField::class, $instance);
    }

    public function test_interface_has_exactly_three_methods(): void
    {
        $reflection = new \ReflectionClass(UploadField::class);

        $this->assertCount(3, $reflection->getMethods());
    }
}
