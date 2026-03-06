<?php

namespace Dcat\Admin\Tests\Unit\Scaffold;

use Dcat\Admin\Exception\AdminException;
use Dcat\Admin\Scaffold\ControllerCreator;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Filesystem\Filesystem;
use Mockery;

class ControllerCreatorTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_get_stub_returns_correct_path(): void
    {
        $creator = new ControllerCreator('App\\Admin\\Controllers\\UserController', Mockery::mock(Filesystem::class));

        $stub = $creator->getStub();

        $this->assertStringContainsString('controller.stub', $stub);
        $this->assertFileExists($stub);
    }

    public function test_get_path_returns_string(): void
    {
        $creator = new ControllerCreator('App\\Admin\\Controllers\\UserController', Mockery::mock(Filesystem::class));

        $result = $creator->getPath('App\\Admin\\Controllers\\UserController');

        $this->assertIsString($result);
        $this->assertStringEndsWith('UserController.php', $result);
    }

    public function test_create_throws_if_file_exists(): void
    {
        $files = Mockery::mock(Filesystem::class);
        $files->shouldReceive('makeDirectory')->andReturn(true);
        $files->shouldReceive('exists')->andReturn(true);

        $creator = new ControllerCreator('App\\Admin\\Controllers\\UserController', $files);

        $this->expectException(AdminException::class);
        $this->expectExceptionMessage('already exists');

        $creator->create('App\\Models\\User');
    }

    public function test_create_writes_file(): void
    {
        $stubContent = 'DummyNamespace DummyClass DummyModelNamespace DummyModel DummyTitle {controller} {grid} {form} {show}';

        $files = Mockery::mock(Filesystem::class);
        $files->shouldReceive('exists')->andReturn(false);
        $files->shouldReceive('get')->andReturn($stubContent);
        $files->shouldReceive('makeDirectory')->andReturn(true);
        $files->shouldReceive('put')->once()->andReturn(true);
        $files->shouldReceive('chmod')->once()->andReturn(true);

        $creator = new ControllerCreator('App\\Admin\\Controllers\\UserController', $files);

        $path = $creator->create('App\\Models\\User');

        $this->assertIsString($path);
        $this->assertStringEndsWith('UserController.php', $path);

        Mockery::close();
        $this->addToAssertionCount(1);
    }

    public function test_create_generates_correct_class_name(): void
    {
        $stubContent = 'DummyNamespace DummyClass DummyModelNamespace DummyModel DummyTitle {controller} {grid} {form} {show}';

        $writtenContent = null;
        $files = Mockery::mock(Filesystem::class);
        $files->shouldReceive('exists')->andReturn(false);
        $files->shouldReceive('get')->andReturn($stubContent);
        $files->shouldReceive('makeDirectory')->andReturn(true);
        $files->shouldReceive('put')->once()->andReturnUsing(function ($path, $content) use (&$writtenContent) {
            $writtenContent = $content;

            return true;
        });
        $files->shouldReceive('chmod')->andReturn(true);

        $creator = new ControllerCreator('App\\Admin\\Controllers\\UserController', $files);
        $creator->create('App\\Models\\User');

        $this->assertStringContainsString('App\\Admin\\Controllers', $writtenContent);
        $this->assertStringContainsString('UserController', $writtenContent);
        $this->assertStringContainsString('App\\Models\\User', $writtenContent);
    }

    public function test_constructor_sets_name(): void
    {
        $files = Mockery::mock(Filesystem::class);
        $creator = new ControllerCreator('App\\Admin\\Controllers\\PostController', $files);

        $ref = new \ReflectionProperty($creator, 'name');
        $ref->setAccessible(true);

        $this->assertSame('App\\Admin\\Controllers\\PostController', $ref->getValue($creator));
    }

    public function test_constructor_uses_app_files_when_null(): void
    {
        $creator = new ControllerCreator('App\\Admin\\Controllers\\UserController');

        $ref = new \ReflectionProperty($creator, 'files');
        $ref->setAccessible(true);

        $this->assertInstanceOf(Filesystem::class, $ref->getValue($creator));
    }

    public function test_create_replaces_scaffold_placeholders_from_generator_methods(): void
    {
        $stubContent = 'DummyNamespace DummyClass DummyModelNamespace DummyModel DummyTitle {controller} {grid} {form} {show}';

        $writtenContent = null;
        $files = Mockery::mock(Filesystem::class);
        $files->shouldReceive('exists')->andReturn(false);
        $files->shouldReceive('get')->andReturn($stubContent);
        $files->shouldReceive('makeDirectory')->andReturn(true);
        $files->shouldReceive('put')->once()->andReturnUsing(function ($path, $content) use (&$writtenContent) {
            $writtenContent = $content;

            return true;
        });
        $files->shouldReceive('chmod')->andReturn(true);

        $creator = new ControllerCreator('App\\Admin\\Controllers\\UserController', $files);
        $creator->create('App\\Models\\User');

        $this->assertNotNull($writtenContent);
        $this->assertStringNotContainsString('{grid}', $writtenContent);
        $this->assertStringNotContainsString('{form}', $writtenContent);
        $this->assertStringNotContainsString('{show}', $writtenContent);
        $this->assertStringContainsString("\$grid->column('id')->sortable();", $writtenContent);
        $this->assertStringContainsString("\$form->display('id');", $writtenContent);
        $this->assertStringContainsString("\$show->field('id');", $writtenContent);
    }
}
