<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\WebUploader;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class FakeWebUploaderField
{
    use WebUploader;

    public const FILE_DELETE_FLAG = '_remove_';

    public array $options = [
    ];

    public array $rulesCalled = [];

    public $form;

    public function rules(string $rule): void
    {
        $this->rulesCalled[] = $rule;
    }

    public function options(array $options): void
    {
        $this->options = $options;
    }

    public function column(): string
    {
        return 'avatar';
    }

    public function getElementName(): string
    {
        return 'avatar';
    }

    protected function initialPreviewConfig(): array
    {
        return ['url' => 'preview'];
    }

    public function exposeSetUpDefaultOptions(): void
    {
        $this->setUpDefaultOptions();
    }

    public function exposeSetDefaultServer(): void
    {
        $this->setDefaultServer();
    }

    public function exposeSetupPreviewOptions(): void
    {
        $this->setupPreviewOptions();
    }
}

class WebUploaderTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function makeField(): FakeWebUploaderField
    {
        return new FakeWebUploaderField;
    }

    public function test_accept_and_mime_types_set_accept_options(): void
    {
        $field = $this->makeField();

        $field->accept('jpg,png', 'image/*')->mimeTypes('image/jpeg,image/png');

        $this->assertSame('jpg,png', $field->options['accept']['extensions']);
        $this->assertSame('image/jpeg,image/png', $field->options['accept']['mimeTypes']);
    }

    public function test_chunk_size_enables_chunked_and_converts_kb_to_bytes(): void
    {
        $field = $this->makeField();
        $field->chunkSize(512);

        $this->assertTrue($field->options['chunked']);
        $this->assertSame(512 * 1024, $field->options['chunkSize']);
    }

    public function test_max_size_sets_rule_and_single_size_limit(): void
    {
        $field = $this->makeField();
        $field->maxSize(1024);

        $this->assertContains('max:1024', $field->rulesCalled);
        $this->assertSame(1024 * 1024, $field->options['fileSingleSizeLimit']);
    }

    public function test_url_sets_server_and_delete_url(): void
    {
        $field = $this->makeField();
        $field->url('upload/avatar');

        $this->assertStringContainsString('/upload/avatar', $field->options['server']);
        $this->assertStringContainsString('/upload/avatar', $field->options['deleteUrl']);
    }

    public function test_toggle_methods_update_options_flags(): void
    {
        $field = $this->makeField();
        $field->autoUpload(false)->autoSave(false)->removable()->downloadable(false)->compress(['quality' => 80])->threads(3);

        $this->assertFalse($field->options['autoUpload']);
        $this->assertFalse($field->options['autoUpdateColumn']);
        $this->assertFalse($field->options['removable']);
        $this->assertFalse($field->options['downloadable']);
        $this->assertSame(['quality' => 80], $field->options['compress']);
        $this->assertSame(3, $field->options['threads']);
    }

    public function test_form_and_delete_data_are_merged(): void
    {
        $field = $this->makeField();
        $field->options['formData'] = [];
        $field->options['deleteData'] = [];

        $field->withFormData(['a' => 1])->withDeleteData(['b' => 2]);

        $this->assertSame(1, $field->options['formData']['a']);
        $this->assertSame(2, $field->options['deleteData']['b']);
    }

    public function test_setup_default_options_populates_required_keys(): void
    {
        $field = $this->makeField();
        $field->form = new class
        {
            public function getKey(): int
            {
                return 15;
            }
        };

        $field->exposeSetUpDefaultOptions();

        $this->assertSame('avatar', $field->options['elementName']);
        $this->assertSame(15, $field->options['formData']['primary_key']);
        $this->assertSame(15, $field->options['deleteData']['primary_key']);
        $this->assertContains('lang', array_keys($field->options));
    }

    public function test_set_default_server_uses_form_action_and_editing_method_put(): void
    {
        $field = $this->makeField();
        $field->options['formData'] = [];
        $field->options['deleteData'] = [];
        $field->form = new class
        {
            public function action(): string
            {
                return 'http://localhost/admin/users/1';
            }

            public function builder()
            {
                return new class
                {
                    public function isEditing(): bool
                    {
                        return true;
                    }
                };
            }
        };

        $field->exposeSetDefaultServer();

        $this->assertSame('http://localhost/admin/users/1', $field->options['server']);
        $this->assertSame('http://localhost/admin/users/1', $field->options['updateServer']);
        $this->assertSame('http://localhost/admin/users/1', $field->options['deleteUrl']);
        $this->assertSame('PUT', $field->options['formData']['_method']);
        $this->assertSame('PUT', $field->options['deleteData']['_method']);
        $this->assertTrue($field->options['autoUpdateColumn']);
    }

    public function test_setup_preview_options_uses_initial_preview_config(): void
    {
        $field = $this->makeField();
        $field->exposeSetupPreviewOptions();

        $this->assertSame(['url' => 'preview'], $field->options['preview']);
    }

    public function test_get_create_url_removes_trailing_create_segment(): void
    {
        $request = \Illuminate\Http\Request::create('/admin/users/create', 'GET');
        $this->app->instance('request', $request);

        $field = $this->makeField();
        $url = $field->getCreateUrl();

        $this->assertStringEndsWith('/admin/users', $url);
    }
}
