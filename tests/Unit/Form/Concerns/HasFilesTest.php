<?php

namespace Dcat\Admin\Tests\Unit\Form\Concerns;

use Dcat\Admin\Contracts\UploadField;
use Dcat\Admin\Form\Concerns\HasFiles;
use Dcat\Admin\Tests\TestCase;
use Mockery;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class HasFilesTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function invokeProtectedMethod(object $object, string $method, array $arguments = [])
    {
        $reflection = new \ReflectionMethod($object, $method);
        $reflection->setAccessible(true);

        return $reflection->invokeArgs($object, $arguments);
    }

    public function test_handle_upload_file_method_is_protected_with_data_parameter(): void
    {
        $method = new \ReflectionMethod(HasFilesTestDouble::class, 'handleUploadFile');

        $this->assertTrue($method->isProtected());
        $this->assertCount(1, $method->getParameters());
        $this->assertSame('data', $method->getParameters()[0]->getName());
    }

    public function test_delete_related_methods_are_protected(): void
    {
        $deleteFileWhenCreating = new \ReflectionMethod(HasFilesTestDouble::class, 'deleteFileWhenCreating');
        $deleteFile = new \ReflectionMethod(HasFilesTestDouble::class, 'deleteFile');
        $handleFileDelete = new \ReflectionMethod(HasFilesTestDouble::class, 'handleFileDelete');

        $this->assertTrue($deleteFileWhenCreating->isProtected());
        $this->assertTrue($deleteFile->isProtected());
        $this->assertTrue($handleFileDelete->isProtected());
    }

    public function test_find_field_and_relation_field_methods_are_public(): void
    {
        $findFieldByName = new \ReflectionMethod(HasFilesTestDouble::class, 'findFieldByName');
        $getFieldByRelationName = new \ReflectionMethod(HasFilesTestDouble::class, 'getFieldByRelationName');
        $deleteFiles = new \ReflectionMethod(HasFilesTestDouble::class, 'deleteFiles');

        $this->assertTrue($findFieldByName->isPublic());
        $this->assertTrue($getFieldByRelationName->isPublic());
        $this->assertTrue($deleteFiles->isPublic());
    }

    public function test_get_upload_field_column_returns_column_when_method_exists(): void
    {
        $form = new HasFilesTestDouble;

        $field = new UploadFieldWithColumn;

        $column = $this->invokeProtectedMethod($form, 'getUploadFieldColumn', [$field]);

        $this->assertSame('avatar', $column);
    }

    public function test_get_upload_field_column_throws_when_column_method_missing(): void
    {
        $form = new HasFilesTestDouble;

        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('Upload field must implement column() method.');

        $this->invokeProtectedMethod($form, 'getUploadFieldColumn', [new UploadFieldWithoutColumn]);
    }

    public function test_set_upload_field_original_calls_set_original_when_available(): void
    {
        $form = new HasFilesTestDouble;
        $field = new UploadFieldWithSetOriginal;

        $this->invokeProtectedMethod($form, 'setUploadFieldOriginal', [$field, ['avatar' => 'a.jpg']]);

        $this->assertSame(['avatar' => 'a.jpg'], $field->original);
    }

    public function test_set_upload_field_original_throws_when_set_original_missing(): void
    {
        $form = new HasFilesTestDouble;

        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('Upload field must implement setOriginal() method.');

        $this->invokeProtectedMethod($form, 'setUploadFieldOriginal', [new UploadFieldWithColumn, ['avatar' => 'a.jpg']]);
    }
}

class HasFilesTestDouble
{
    use HasFiles;
}

class UploadFieldWithColumn implements UploadField
{
    public function column(): string
    {
        return 'avatar';
    }

    public function upload(UploadedFile $file) {}

    public function destroy() {}

    public function deleteFile($path) {}
}

class UploadFieldWithSetOriginal extends UploadFieldWithColumn
{
    public array $original = [];

    public function setOriginal(array $input): void
    {
        $this->original = $input;
    }
}

class UploadFieldWithoutColumn implements UploadField
{
    public function upload(UploadedFile $file) {}

    public function destroy() {}

    public function deleteFile($path) {}
}
