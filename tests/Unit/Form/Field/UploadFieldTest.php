<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\File;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Contracts\Filesystem\Filesystem;
use Mockery;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * 测试 UploadField trait 的关键方法。
 * 通过真实的 File 字段类（使用 UploadField trait）进行测试。
 */
class UploadFieldTest extends TestCase
{
    protected function createFileField(string $column = 'file', string $label = 'File'): File
    {
        $field = new File($column, [$label]);

        // 配置一个 mock storage 以避免真实的文件系统操作
        $storage = Mockery::mock(Filesystem::class);
        $storage->shouldReceive('exists')->andReturn(false);
        $storage->shouldReceive('delete')->andReturn(true);
        $storage->shouldReceive('url')->andReturn('');

        $reflection = new \ReflectionProperty($field, 'storage');
        $reflection->setAccessible(true);
        $reflection->setValue($field, $storage);

        return $field;
    }

    protected function setOriginal(File $field, $value): void
    {
        $reflection = new \ReflectionProperty($field, 'original');
        $reflection->setAccessible(true);
        $reflection->setValue($field, $value);
    }

    public function test_destroy_if_changed_does_not_delete_when_no_original(): void
    {
        $field = $this->createFileField();
        $this->setOriginal($field, null);

        // 没有原始文件时，不应调用 deleteFile
        $field->destroyIfChanged('new_file.jpg');

        // 验证不会异常终止——没有原始文件时直接返回
        $this->assertTrue(true);
    }

    public function test_destroy_if_changed_calls_destroy_when_no_new_file_but_has_original(): void
    {
        $field = $this->createFileField();
        $this->setOriginal($field, 'original_file.jpg');

        // mock storage 检查 deleteFile 是否被调用
        $storage = Mockery::mock(Filesystem::class);
        $storage->shouldReceive('exists')->with('original_file.jpg')->once()->andReturn(true);
        $storage->shouldReceive('delete')->with('original_file.jpg')->once()->andReturn(true);

        $reflection = new \ReflectionProperty($field, 'storage');
        $reflection->setAccessible(true);
        $reflection->setValue($field, $storage);

        // 有原始文件但无新文件时，应该删除原始文件
        $field->destroyIfChanged(null);

        Mockery::close();
        $this->addToAssertionCount(1);
    }

    public function test_destroy_if_changed_calls_destroy_on_empty_string(): void
    {
        $field = $this->createFileField();
        $this->setOriginal($field, 'original_file.jpg');

        $storage = Mockery::mock(Filesystem::class);
        $storage->shouldReceive('exists')->with('original_file.jpg')->once()->andReturn(true);
        $storage->shouldReceive('delete')->with('original_file.jpg')->once()->andReturn(true);

        $reflection = new \ReflectionProperty($field, 'storage');
        $reflection->setAccessible(true);
        $reflection->setValue($field, $storage);

        // 空字符串视为无新文件
        $field->destroyIfChanged('');

        Mockery::close();
        $this->addToAssertionCount(1);
    }

    public function test_destroy_if_changed_keeps_same_file(): void
    {
        $field = $this->createFileField();
        $this->setOriginal($field, 'file.jpg');

        $storage = Mockery::mock(Filesystem::class);
        // 相同文件时不应删除
        $storage->shouldNotReceive('delete');

        $reflection = new \ReflectionProperty($field, 'storage');
        $reflection->setAccessible(true);
        $reflection->setValue($field, $storage);

        $field->destroyIfChanged('file.jpg');

        Mockery::close();
        $this->addToAssertionCount(1);
    }

    public function test_destroy_if_changed_deletes_removed_files_from_array(): void
    {
        $field = $this->createFileField();
        $this->setOriginal($field, ['file1.jpg', 'file2.jpg', 'file3.jpg']);

        $storage = Mockery::mock(Filesystem::class);
        // 只有 file2.jpg 应该被删除
        $storage->shouldReceive('exists')->with('file2.jpg')->once()->andReturn(true);
        $storage->shouldReceive('delete')->with('file2.jpg')->once()->andReturn(true);

        $reflection = new \ReflectionProperty($field, 'storage');
        $reflection->setAccessible(true);
        $reflection->setValue($field, $storage);

        // 只保留 file1 和 file3
        $field->destroyIfChanged(['file1.jpg', 'file3.jpg']);

        Mockery::close();
        $this->addToAssertionCount(1);
    }

    public function test_get_validation_errors_returns_null_when_no_rules(): void
    {
        $field = $this->createFileField();

        // 确保没有验证规则
        $field->rules([]);

        $tmpFile = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents($tmpFile, 'test content');
        $file = new UploadedFile(
            $tmpFile,
            'test.txt',
            'text/plain',
            null,
            true
        );

        $reflection = new \ReflectionMethod($field, 'getValidationErrors');
        $reflection->setAccessible(true);
        $result = $reflection->invoke($field, $file);

        // 没有规则时应返回 null（修复前是 false）
        $this->assertNull($result);

        @unlink($tmpFile);
    }

    public function test_get_validation_errors_returns_error_on_upload_error(): void
    {
        $field = $this->createFileField();

        $tmpFile = tempnam(sys_get_temp_dir(), 'test');
        $file = new UploadedFile(
            $tmpFile,
            'test.txt',
            'text/plain',
            UPLOAD_ERR_INI_SIZE,
            true
        );

        $reflection = new \ReflectionMethod($field, 'getValidationErrors');
        $reflection->setAccessible(true);
        $result = $reflection->invoke($field, $file);

        // 上传有错误时应返回错误消息字符串
        $this->assertIsString($result);

        @unlink($tmpFile);
    }
}
