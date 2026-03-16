<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Http\Forms;

use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Exception\RuntimeException;
use Dcat\Admin\Http\Forms\InstallFromLocal;
use Dcat\Admin\Http\JsonResponse;
use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Widgets\Form;
use Mockery;

class InstallFromLocalTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_extends_form_and_implements_lazy_renderable(): void
    {
        $form = new InstallFromLocal;

        $this->assertInstanceOf(Form::class, $form);
        $this->assertInstanceOf(LazyRenderable::class, $form);
    }

    public function test_handle_signature_accepts_array_input(): void
    {
        $reflection = new \ReflectionMethod(InstallFromLocal::class, 'handle');
        $parameters = $reflection->getParameters();

        $this->assertCount(1, $parameters);
        $this->assertSame('input', $parameters[0]->getName());
        $this->assertSame('array', $parameters[0]->getType()->getName());
    }

    public function test_handle_returns_error_response_for_empty_extension(): void
    {
        $form = new InstallFromLocal;

        $response = $form->handle(['extension' => '']);

        $this->assertInstanceOf(JsonResponse::class, $response);

        $payload = $response->toArray();
        $this->assertFalse($payload['status']);
        $this->assertSame('Invalid arguments.', $payload['data']['message']);
        $this->assertSame('error', $payload['data']['type']);
    }

    public function test_disk_uses_admin_extension_disk_config_when_present(): void
    {
        config()->set('admin.extension.disk', 's3-private');

        $form = new TestableInstallFromLocal;

        $this->assertSame('s3-private', $form->publicDisk());
    }

    public function test_disk_defaults_to_local_when_config_missing(): void
    {
        config()->set('admin.extension.disk', null);

        $form = new TestableInstallFromLocal;

        $this->assertSame('local', $form->publicDisk());
    }

    public function test_get_file_path_uses_disk_root_config(): void
    {
        config()->set('admin.extension.disk', 'local');
        config()->set('filesystems.disks.local.root', '/tmp/admin-extensions');

        $form = new TestableInstallFromLocal;

        $this->assertSame('/tmp/admin-extensions/demo.zip', $form->publicGetFilePath('demo.zip'));
    }

    public function test_get_file_path_throws_when_disk_root_missing(): void
    {
        config()->set('admin.extension.disk', 'missing-disk');
        config()->set('filesystems.disks.missing-disk.root', null);

        $form = new TestableInstallFromLocal;

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Missing 'root' for disk [missing-disk].");

        $form->publicGetFilePath('demo.zip');
    }

    public function test_get_file_path_and_disk_methods_are_protected(): void
    {
        $getFilePath = new \ReflectionMethod(InstallFromLocal::class, 'getFilePath');
        $disk = new \ReflectionMethod(InstallFromLocal::class, 'disk');

        $this->assertTrue($getFilePath->isProtected());
        $this->assertTrue($disk->isProtected());
    }
}

class TestableInstallFromLocal extends InstallFromLocal
{
    public function publicDisk(): string
    {
        return $this->disk();
    }

    public function publicGetFilePath(string $file): string
    {
        return $this->getFilePath($file);
    }
}
