<?php

namespace Dcat\Admin\Tests\Unit\Scaffold;

use Dcat\Admin\Exception\AdminException;
use Dcat\Admin\Scaffold\ModelCreator;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Filesystem\Filesystem;
use Mockery;

class ModelCreatorTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_get_stub_returns_correct_path(): void
    {
        $creator = new ModelCreator('users', 'App\\Models\\User', Mockery::mock(Filesystem::class));

        $stub = $creator->getStub();

        $this->assertStringContainsString('model.stub', $stub);
        $this->assertFileExists($stub);
    }

    public function test_get_path_delegates_to_helper(): void
    {
        $creator = new ModelCreator('users', 'App\\Models\\User', Mockery::mock(Filesystem::class));

        $result = $creator->getPath('App\\Models\\User');

        $this->assertIsString($result);
        $this->assertStringEndsWith('User.php', $result);
    }

    public function test_replace_space_removes_triple_newlines(): void
    {
        $creator = new ModelCreator('users', 'App\\Models\\User', Mockery::mock(Filesystem::class));

        $input = "line1\n\n\nline2";
        $result = $creator->replaceSpace($input);

        $this->assertEquals("line1\n\nline2", $result);
    }

    public function test_replace_space_removes_indented_empty_lines(): void
    {
        $creator = new ModelCreator('users', 'App\\Models\\User', Mockery::mock(Filesystem::class));

        $input = "line1\n    \nline2";
        $result = $creator->replaceSpace($input);

        $this->assertEquals('line1line2', $result);
    }

    public function test_create_throws_if_file_exists(): void
    {
        $files = Mockery::mock(Filesystem::class);
        $files->shouldReceive('exists')->andReturn(true);

        $creator = new ModelCreator('users', 'App\\Models\\User', $files);

        $this->expectException(AdminException::class);
        $this->expectExceptionMessage('already exists');

        $creator->create('id', true, false);
    }

    public function test_create_writes_file(): void
    {
        $stubContent = 'DummyNamespace DummyClass DummyModelKey DummyModelTable DummyTimestamp DummyFillable DummyImportSoftDeletesTrait DummyUseSoftDeletesTrait DummyImportDateTimeFormatterTrait DummyUseDateTimeFormatterTrait';

        $files = Mockery::mock(Filesystem::class);
        $files->shouldReceive('exists')->andReturn(false);
        $files->shouldReceive('get')->andReturn($stubContent);
        $files->shouldReceive('makeDirectory')->andReturn(true);
        $files->shouldReceive('put')->once()->andReturn(true);
        $files->shouldReceive('chmod')->once()->andReturn(true);

        $creator = new ModelCreator('users', 'App\\Models\\User', $files);

        $path = $creator->create('id', true, false);

        $this->assertIsString($path);
        $this->assertStringEndsWith('User.php', $path);

        // Mockery will verify put was called
        Mockery::close();
        $this->addToAssertionCount(1);
    }

    public function test_constructor_sets_table_name_and_name(): void
    {
        $files = Mockery::mock(Filesystem::class);
        $creator = new ModelCreator('posts', 'App\\Models\\Post', $files);

        $ref = new \ReflectionProperty($creator, 'tableName');
        $ref->setAccessible(true);
        $this->assertEquals('posts', $ref->getValue($creator));

        $ref2 = new \ReflectionProperty($creator, 'name');
        $ref2->setAccessible(true);
        $this->assertEquals('App\\Models\\Post', $ref2->getValue($creator));
    }

    public function test_constructor_uses_app_files_when_null(): void
    {
        $creator = new ModelCreator('users', 'App\\Models\\User');

        $ref = new \ReflectionProperty($creator, 'files');
        $ref->setAccessible(true);

        $this->assertInstanceOf(Filesystem::class, $ref->getValue($creator));
    }

    public function test_create_with_soft_deletes(): void
    {
        $stubContent = 'DummyNamespace DummyClass DummyModelKey DummyModelTable DummyTimestamp DummyFillable DummyImportSoftDeletesTrait DummyUseSoftDeletesTrait DummyImportDateTimeFormatterTrait DummyUseDateTimeFormatterTrait';

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

        $creator = new ModelCreator('users', 'App\\Models\\User', $files);
        $creator->create('id', true, true);

        $this->assertStringContainsString('use SoftDeletes;', $writtenContent);
        $this->assertStringContainsString('use Illuminate\\Database\\Eloquent\\SoftDeletes;', $writtenContent);
    }

    public function test_create_with_custom_primary_key(): void
    {
        $stubContent = 'DummyNamespace DummyClass DummyModelKey DummyModelTable DummyTimestamp DummyFillable DummyImportSoftDeletesTrait DummyUseSoftDeletesTrait DummyImportDateTimeFormatterTrait DummyUseDateTimeFormatterTrait';

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

        $creator = new ModelCreator('users', 'App\\Models\\User', $files);
        $creator->create('uuid', true, false);

        $this->assertStringContainsString("\$primaryKey = 'uuid'", $writtenContent);
    }

    public function test_create_without_timestamps(): void
    {
        $stubContent = 'DummyNamespace DummyClass DummyModelKey DummyModelTable DummyTimestamp DummyFillable DummyImportSoftDeletesTrait DummyUseSoftDeletesTrait DummyImportDateTimeFormatterTrait DummyUseDateTimeFormatterTrait';

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

        $creator = new ModelCreator('users', 'App\\Models\\User', $files);
        $creator->create('id', false, false);

        $this->assertStringContainsString('$timestamps = false', $writtenContent);
    }
}
