<?php

namespace Dcat\Admin\Tests\Unit\Http\Forms;

use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Http\Forms\InstallFromLocal;
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

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(InstallFromLocal::class));
    }

    public function test_is_subclass_of_widgets_form(): void
    {
        $this->assertTrue(is_subclass_of(InstallFromLocal::class, Form::class));
    }

    public function test_implements_lazy_renderable(): void
    {
        $reflection = new \ReflectionClass(InstallFromLocal::class);
        $this->assertTrue($reflection->implementsInterface(LazyRenderable::class));
    }

    public function test_method_handle_exists(): void
    {
        $this->assertTrue(method_exists(InstallFromLocal::class, 'handle'));
    }

    public function test_method_form_exists(): void
    {
        $this->assertTrue(method_exists(InstallFromLocal::class, 'form'));
    }

    public function test_method_get_file_path_exists(): void
    {
        $this->assertTrue(method_exists(InstallFromLocal::class, 'getFilePath'));
    }

    public function test_method_disk_exists(): void
    {
        $this->assertTrue(method_exists(InstallFromLocal::class, 'disk'));
    }

    public function test_handle_method_accepts_array_parameter(): void
    {
        $reflection = new \ReflectionMethod(InstallFromLocal::class, 'handle');
        $parameters = $reflection->getParameters();

        $this->assertCount(1, $parameters);
        $this->assertEquals('input', $parameters[0]->getName());
        $this->assertEquals('array', $parameters[0]->getType()->getName());
    }

    public function test_get_file_path_is_protected(): void
    {
        $reflection = new \ReflectionMethod(InstallFromLocal::class, 'getFilePath');
        $this->assertTrue($reflection->isProtected());
    }

    public function test_disk_is_protected(): void
    {
        $reflection = new \ReflectionMethod(InstallFromLocal::class, 'disk');
        $this->assertTrue($reflection->isProtected());
    }
}
