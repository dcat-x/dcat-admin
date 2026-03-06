<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\File;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class FileTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function createFileField(string $column = 'document', string $label = 'Document'): File
    {
        $field = new File($column, [$label]);

        $storage = Mockery::mock(\Illuminate\Contracts\Filesystem\Filesystem::class);
        $storage->shouldReceive('exists')->andReturn(false)->byDefault();
        $storage->shouldReceive('delete')->andReturn(true)->byDefault();
        $storage->shouldReceive('url')->andReturn('')->byDefault();

        $reflection = new \ReflectionProperty($field, 'storage');
        $reflection->setAccessible(true);
        $reflection->setValue($field, $storage);

        return $field;
    }

    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    protected function setProtectedProperty(object $object, string $property, mixed $value): void
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);
        $reflection->setValue($object, $value);
    }

    // -------------------------------------------------------
    // constructor & default options
    // -------------------------------------------------------

    public function test_constructor_sets_default_options(): void
    {
        $field = $this->createFileField();

        $options = $this->getProtectedProperty($field, 'options');

        $this->assertFalse($options['isImage'] ?? null);
        $this->assertFalse($options['chunked'] ?? null);
    }

    public function test_options_has_events_key(): void
    {
        $field = $this->createFileField();

        $options = $this->getProtectedProperty($field, 'options');

        $this->assertIsArray($options['events'] ?? null);
    }

    public function test_options_has_override_key(): void
    {
        $field = $this->createFileField();

        $options = $this->getProtectedProperty($field, 'options');

        $this->assertFalse($options['override'] ?? null);
    }

    // -------------------------------------------------------
    // defaultDirectory()
    // -------------------------------------------------------

    public function test_default_directory_returns_config_value(): void
    {
        $this->app['config']->set('admin.upload.directory.file', 'files');

        $field = $this->createFileField();

        $this->assertSame('files', $field->defaultDirectory());
    }

    public function test_default_directory_returns_null_when_config_not_set(): void
    {
        $this->app['config']->set('admin.upload.directory.file', null);

        $field = $this->createFileField();

        $this->assertNull($field->defaultDirectory());
    }

    // -------------------------------------------------------
    // on() / once() - event listeners
    // -------------------------------------------------------

    public function test_on_adds_event(): void
    {
        $field = $this->createFileField();

        $result = $field->on('fileQueued', 'console.log("queued")');

        $this->assertSame($field, $result);

        $options = $this->getProtectedProperty($field, 'options');
        $this->assertCount(1, $options['events']);
        $this->assertSame('fileQueued', $options['events'][0]['event']);
        $this->assertFalse($options['events'][0]['once']);
    }

    public function test_on_with_once_flag(): void
    {
        $field = $this->createFileField();

        $field->on('uploadComplete', 'console.log("done")', true);

        $options = $this->getProtectedProperty($field, 'options');
        $this->assertTrue($options['events'][0]['once']);
    }

    public function test_once_adds_event_with_once_flag(): void
    {
        $field = $this->createFileField();

        $result = $field->once('uploadStart', 'console.log("start")');

        $this->assertSame($field, $result);

        $options = $this->getProtectedProperty($field, 'options');
        $this->assertCount(1, $options['events']);
        $this->assertTrue($options['events'][0]['once']);
    }

    public function test_multiple_events_can_be_added(): void
    {
        $field = $this->createFileField();

        $field->on('event1', 'script1');
        $field->on('event2', 'script2');
        $field->once('event3', 'script3');

        $options = $this->getProtectedProperty($field, 'options');
        $this->assertCount(3, $options['events']);
    }

    // -------------------------------------------------------
    // override()
    // -------------------------------------------------------

    public function test_override_enables_override(): void
    {
        $field = $this->createFileField();

        $result = $field->override();

        $this->assertSame($field, $result);

        $options = $this->getProtectedProperty($field, 'options');
        $this->assertTrue($options['override']);
    }

    public function test_override_can_be_disabled(): void
    {
        $field = $this->createFileField();

        $field->override(true);
        $field->override(false);

        $options = $this->getProtectedProperty($field, 'options');
        $this->assertFalse($options['override']);
    }

    // -------------------------------------------------------
    // disable()
    // -------------------------------------------------------

    public function test_disable_sets_disabled_option(): void
    {
        $field = $this->createFileField();

        $result = $field->disable();

        $this->assertSame($field, $result);

        $options = $this->getProtectedProperty($field, 'options');
        $this->assertTrue($options['disabled']);
    }

    public function test_disable_can_be_toggled(): void
    {
        $field = $this->createFileField();

        $field->disable(true);
        $field->disable(false);

        $options = $this->getProtectedProperty($field, 'options');
        $this->assertFalse($options['disabled']);
    }

    // -------------------------------------------------------
    // UploadField trait methods via File
    // -------------------------------------------------------

    public function test_retainable_sets_flag(): void
    {
        $field = $this->createFileField();

        $result = $field->retainable();

        $this->assertSame($field, $result);
        $this->assertTrue($this->getProtectedProperty($field, 'retainable'));
    }

    public function test_retainable_can_be_disabled(): void
    {
        $field = $this->createFileField();

        $field->retainable(true);
        $field->retainable(false);

        $this->assertFalse($this->getProtectedProperty($field, 'retainable'));
    }

    public function test_unique_name_sets_flag(): void
    {
        $field = $this->createFileField();

        $result = $field->uniqueName();

        $this->assertSame($field, $result);
        $this->assertTrue($this->getProtectedProperty($field, 'useUniqueName'));
    }

    public function test_sequence_name_sets_flag(): void
    {
        $field = $this->createFileField();

        $result = $field->sequenceName();

        $this->assertSame($field, $result);
        $this->assertTrue($this->getProtectedProperty($field, 'useSequenceName'));
    }

    public function test_save_full_url_sets_flag(): void
    {
        $field = $this->createFileField();

        $result = $field->saveFullUrl();

        $this->assertSame($field, $result);
        $this->assertTrue($this->getProtectedProperty($field, 'saveFullUrl'));
    }

    public function test_move_sets_directory_and_name(): void
    {
        $field = $this->createFileField();

        $result = $field->move('uploads/docs', 'readme.pdf');

        $this->assertSame($field, $result);
        $this->assertSame('uploads/docs', $this->getProtectedProperty($field, 'directory'));
        $this->assertSame('readme.pdf', $this->getProtectedProperty($field, 'name'));
    }

    public function test_dir_sets_directory(): void
    {
        $field = $this->createFileField();

        $result = $field->dir('uploads/files');

        $this->assertSame($field, $result);
        $this->assertSame('uploads/files', $this->getProtectedProperty($field, 'directory'));
    }

    public function test_storage_permission_sets_permission(): void
    {
        $field = $this->createFileField();

        $result = $field->storagePermission('public');

        $this->assertSame($field, $result);
        $this->assertSame('public', $this->getProtectedProperty($field, 'storagePermission'));
    }

    // -------------------------------------------------------
    // WebUploader trait methods via File
    // -------------------------------------------------------

    public function test_accept_sets_options(): void
    {
        $field = $this->createFileField();

        $result = $field->accept('pdf,doc,docx', 'application/*');

        $this->assertSame($field, $result);

        $options = $this->getProtectedProperty($field, 'options');
        $this->assertSame('pdf,doc,docx', $options['accept']['extensions']);
        $this->assertSame('application/*', $options['accept']['mimeTypes']);
    }

    public function test_accept_without_mime_types(): void
    {
        $field = $this->createFileField();

        $field->accept('zip,rar');

        $options = $this->getProtectedProperty($field, 'options');
        $this->assertSame('zip,rar', $options['accept']['extensions']);
        $this->assertArrayNotHasKey('mimeTypes', $options['accept']);
    }

    public function test_chunked_enables_chunked_upload(): void
    {
        $field = $this->createFileField();

        $result = $field->chunked();

        $this->assertSame($field, $result);

        $options = $this->getProtectedProperty($field, 'options');
        $this->assertTrue($options['chunked']);
    }

    public function test_chunk_size_sets_size_and_enables_chunked(): void
    {
        $field = $this->createFileField();

        $result = $field->chunkSize(512);

        $this->assertSame($field, $result);

        $options = $this->getProtectedProperty($field, 'options');
        $this->assertSame(512 * 1024, $options['chunkSize']);
        $this->assertTrue($options['chunked']);
    }

    public function test_max_size_sets_limit_and_adds_rule(): void
    {
        $field = $this->createFileField();

        $result = $field->maxSize(2048);

        $this->assertSame($field, $result);

        $options = $this->getProtectedProperty($field, 'options');
        $this->assertSame(2048 * 1024, $options['fileSingleSizeLimit']);
    }

    public function test_threads_sets_thread_count(): void
    {
        $field = $this->createFileField();

        $result = $field->threads(4);

        $this->assertSame($field, $result);

        $options = $this->getProtectedProperty($field, 'options');
        $this->assertSame(4, $options['threads']);
    }

    public function test_auto_upload_sets_option(): void
    {
        $field = $this->createFileField();

        $result = $field->autoUpload();

        $this->assertSame($field, $result);

        $options = $this->getProtectedProperty($field, 'options');
        $this->assertTrue($options['autoUpload']);
    }

    public function test_compress_sets_option(): void
    {
        $field = $this->createFileField();

        $result = $field->compress();

        $this->assertSame($field, $result);

        $options = $this->getProtectedProperty($field, 'options');
        $this->assertTrue($options['compress']);
    }

    public function test_compress_with_array_config(): void
    {
        $field = $this->createFileField();

        $config = ['quality' => 80, 'noCompressIfLarger' => true];
        $field->compress($config);

        $options = $this->getProtectedProperty($field, 'options');
        $this->assertSame($config, $options['compress']);
    }

    public function test_downloadable_sets_option(): void
    {
        $field = $this->createFileField();

        $result = $field->downloadable();

        $this->assertSame($field, $result);

        $options = $this->getProtectedProperty($field, 'options');
        $this->assertTrue($options['downloadable']);
    }

    public function test_removable_sets_option(): void
    {
        $field = $this->createFileField();

        $result = $field->removable();

        $this->assertSame($field, $result);

        $options = $this->getProtectedProperty($field, 'options');
        // removable(true) => options['removable'] = false (inverted logic)
        $this->assertFalse($options['removable']);
    }

    public function test_auto_save_sets_option(): void
    {
        $field = $this->createFileField();

        $result = $field->autoSave();

        $this->assertSame($field, $result);

        $options = $this->getProtectedProperty($field, 'options');
        $this->assertTrue($options['autoUpdateColumn']);
    }

    // -------------------------------------------------------
    // forceOptions()
    // -------------------------------------------------------

    public function test_force_options_sets_file_num_limit_to_one(): void
    {
        $field = $this->createFileField();

        $reflection = new \ReflectionMethod($field, 'forceOptions');
        $reflection->setAccessible(true);
        $reflection->invoke($field);

        $options = $this->getProtectedProperty($field, 'options');
        $this->assertSame(1, $options['fileNumLimit']);
    }

    // -------------------------------------------------------
    // deleteRules() static method
    // -------------------------------------------------------

    public function test_delete_rules_removes_file_related_rules(): void
    {
        $field = $this->createFileField();

        $rules = ['required', 'file', 'max:2048', 'image', 'dimensions:width=100'];

        File::deleteRules($field, $rules);

        $this->assertContains('required', $rules);
        $this->assertNotContains('file', $rules);
        $this->assertNotContains('image', $rules);
    }

    public function test_delete_rules_with_string_input(): void
    {
        $field = $this->createFileField();

        $rules = 'required|file|max:2048';

        File::deleteRules($field, $rules);

        $this->assertIsArray($rules);
        $this->assertContains('required', $rules);
    }

    // -------------------------------------------------------
    // implements UploadFieldInterface
    // -------------------------------------------------------

    public function test_implements_upload_field_interface(): void
    {
        $field = $this->createFileField();

        $this->assertInstanceOf(\Dcat\Admin\Contracts\UploadField::class, $field);
    }
}
