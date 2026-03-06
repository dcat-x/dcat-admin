<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\OssDirectUpload;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class OssDirectUploadTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function createField(string $column = 'attachment'): OssDirectUpload
    {
        return new OssDirectUpload($column, ['Attachment']);
    }

    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    public function test_view_is_admin_form_oss_direct_upload(): void
    {
        $field = $this->createField();

        $this->assertSame('admin::form.oss-direct-upload', $this->getProtectedProperty($field, 'view'));
    }

    public function test_defaults_are_expected(): void
    {
        $field = $this->createField();

        $this->assertSame(500, $this->getProtectedProperty($field, 'maxSizeMb'));
        $this->assertSame(10, $this->getProtectedProperty($field, 'chunkSizeMb'));
        $this->assertSame('file', $this->getProtectedProperty($field, 'uploadType'));
        $this->assertSame('*', $this->getProtectedProperty($field, 'acceptExtensions'));
        $this->assertNull($this->getProtectedProperty($field, 'uploadDirectory'));
        $this->assertNull($this->getProtectedProperty($field, 'acceptMimeTypes'));
        $this->assertNull($this->getProtectedProperty($field, 'stsTokenUrl'));
    }

    public function test_max_size_updates_internal_state_and_returns_self(): void
    {
        $field = $this->createField();

        $result = $field->maxSize(1024);

        $this->assertSame($field, $result);
        $this->assertSame(1024, $this->getProtectedProperty($field, 'maxSizeMb'));
    }

    public function test_chunk_size_updates_internal_state_and_returns_self(): void
    {
        $field = $this->createField();

        $result = $field->chunkSize(25);

        $this->assertSame($field, $result);
        $this->assertSame(25, $this->getProtectedProperty($field, 'chunkSizeMb'));
    }

    public function test_accept_updates_extensions_and_mime_types(): void
    {
        $field = $this->createField();

        $result = $field->accept('jpg,png', 'image/jpeg,image/png');

        $this->assertSame($field, $result);
        $this->assertSame('jpg,png', $this->getProtectedProperty($field, 'acceptExtensions'));
        $this->assertSame('image/jpeg,image/png', $this->getProtectedProperty($field, 'acceptMimeTypes'));
    }

    public function test_upload_type_updates_internal_state_and_returns_self(): void
    {
        $field = $this->createField();

        $result = $field->uploadType('image');

        $this->assertSame($field, $result);
        $this->assertSame('image', $this->getProtectedProperty($field, 'uploadType'));
    }

    public function test_directory_updates_internal_state_and_returns_self(): void
    {
        $field = $this->createField();

        $result = $field->directory('uploads/manual');

        $this->assertSame($field, $result);
        $this->assertSame('uploads/manual', $this->getProtectedProperty($field, 'uploadDirectory'));
    }

    public function test_sts_token_url_updates_internal_state_and_returns_self(): void
    {
        $field = $this->createField();

        $result = $field->stsTokenUrl('/custom/sts-token');

        $this->assertSame($field, $result);
        $this->assertSame('/custom/sts-token', $this->getProtectedProperty($field, 'stsTokenUrl'));
    }

    public function test_accept_keeps_mime_types_null_when_not_provided(): void
    {
        $field = $this->createField();

        $result = $field->accept('pdf,doc');

        $this->assertSame($field, $result);
        $this->assertSame('pdf,doc', $this->getProtectedProperty($field, 'acceptExtensions'));
        $this->assertNull($this->getProtectedProperty($field, 'acceptMimeTypes'));
    }
}
