<?php

namespace Dcat\Admin\Tests\Unit\Form\Concerns;

use Dcat\Admin\Form\Concerns\HasFiles;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class HasFilesTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_trait_exists(): void
    {
        $this->assertTrue(trait_exists(HasFiles::class));
    }

    public function test_has_handle_upload_file_method(): void
    {
        $ref = new \ReflectionMethod(HasFiles::class, 'handleUploadFile');
        $this->assertTrue($ref->isProtected());
    }

    public function test_has_find_field_by_name_method(): void
    {
        $this->assertTrue(method_exists(HasFiles::class, 'findFieldByName'));
    }

    public function test_has_delete_file_when_creating_method(): void
    {
        $ref = new \ReflectionMethod(HasFiles::class, 'deleteFileWhenCreating');
        $this->assertTrue($ref->isProtected());
    }

    public function test_has_delete_file_method(): void
    {
        $ref = new \ReflectionMethod(HasFiles::class, 'deleteFile');
        $this->assertTrue($ref->isProtected());
    }

    public function test_has_get_field_by_relation_name_method(): void
    {
        $this->assertTrue(method_exists(HasFiles::class, 'getFieldByRelationName'));
    }

    public function test_has_delete_files_method(): void
    {
        $this->assertTrue(method_exists(HasFiles::class, 'deleteFiles'));
    }

    public function test_has_handle_file_delete_method(): void
    {
        $ref = new \ReflectionMethod(HasFiles::class, 'handleFileDelete');
        $this->assertTrue($ref->isProtected());
    }
}
