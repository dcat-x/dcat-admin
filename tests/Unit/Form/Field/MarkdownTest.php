<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field;
use Dcat\Admin\Form\Field\Markdown;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class MarkdownTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    // -------------------------------------------------------
    // Class existence and inheritance
    // -------------------------------------------------------

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(Markdown::class));
    }

    public function test_extends_field(): void
    {
        $this->assertTrue(is_subclass_of(Markdown::class, Field::class));
    }

    // -------------------------------------------------------
    // Default property values via reflection
    // -------------------------------------------------------

    public function test_options_default_has_height_500(): void
    {
        $reflection = new \ReflectionClass(Markdown::class);
        $defaults = $reflection->getDefaultProperties();

        $this->assertArrayHasKey('options', $defaults);
        $this->assertArrayHasKey('height', $defaults['options']);
        $this->assertSame(500, $defaults['options']['height']);
    }

    public function test_options_default_has_code_fold_true(): void
    {
        $reflection = new \ReflectionClass(Markdown::class);
        $defaults = $reflection->getDefaultProperties();

        $this->assertTrue($defaults['options']['codeFold']);
    }

    public function test_options_default_has_save_html_to_textarea_true(): void
    {
        $reflection = new \ReflectionClass(Markdown::class);
        $defaults = $reflection->getDefaultProperties();

        $this->assertTrue($defaults['options']['saveHTMLToTextarea']);
    }

    public function test_options_default_has_search_replace_true(): void
    {
        $reflection = new \ReflectionClass(Markdown::class);
        $defaults = $reflection->getDefaultProperties();

        $this->assertTrue($defaults['options']['searchReplace']);
    }

    public function test_options_default_has_emoji_true(): void
    {
        $reflection = new \ReflectionClass(Markdown::class);
        $defaults = $reflection->getDefaultProperties();

        $this->assertTrue($defaults['options']['emoji']);
    }

    public function test_options_default_has_task_list_true(): void
    {
        $reflection = new \ReflectionClass(Markdown::class);
        $defaults = $reflection->getDefaultProperties();

        $this->assertTrue($defaults['options']['taskList']);
    }

    public function test_options_default_has_tocm_true(): void
    {
        $reflection = new \ReflectionClass(Markdown::class);
        $defaults = $reflection->getDefaultProperties();

        $this->assertTrue($defaults['options']['tocm']);
    }

    public function test_options_default_has_tex_true(): void
    {
        $reflection = new \ReflectionClass(Markdown::class);
        $defaults = $reflection->getDefaultProperties();

        $this->assertTrue($defaults['options']['tex']);
    }

    public function test_options_default_has_flow_chart_false(): void
    {
        $reflection = new \ReflectionClass(Markdown::class);
        $defaults = $reflection->getDefaultProperties();

        $this->assertFalse($defaults['options']['flowChart']);
    }

    public function test_options_default_has_sequence_diagram_false(): void
    {
        $reflection = new \ReflectionClass(Markdown::class);
        $defaults = $reflection->getDefaultProperties();

        $this->assertFalse($defaults['options']['sequenceDiagram']);
    }

    public function test_options_default_has_image_upload_true(): void
    {
        $reflection = new \ReflectionClass(Markdown::class);
        $defaults = $reflection->getDefaultProperties();

        $this->assertTrue($defaults['options']['imageUpload']);
    }

    public function test_options_default_has_auto_focus_true(): void
    {
        $reflection = new \ReflectionClass(Markdown::class);
        $defaults = $reflection->getDefaultProperties();

        $this->assertTrue($defaults['options']['autoFocus']);
    }

    public function test_image_upload_directory_default_is_markdown_images(): void
    {
        $reflection = new \ReflectionClass(Markdown::class);
        $defaults = $reflection->getDefaultProperties();

        $this->assertArrayHasKey('imageUploadDirectory', $defaults);
        $this->assertSame('markdown/images', $defaults['imageUploadDirectory']);
    }

    public function test_language_default_is_null(): void
    {
        $reflection = new \ReflectionClass(Markdown::class);
        $defaults = $reflection->getDefaultProperties();

        $this->assertArrayHasKey('language', $defaults);
        $this->assertNull($defaults['language']);
    }

    public function test_disk_default_is_null(): void
    {
        $reflection = new \ReflectionClass(Markdown::class);
        $defaults = $reflection->getDefaultProperties();

        $this->assertArrayHasKey('disk', $defaults);
        $this->assertNull($defaults['disk']);
    }

    // -------------------------------------------------------
    // Method existence
    // -------------------------------------------------------

    public function test_html_decode_method_exists(): void
    {
        $this->assertTrue(method_exists(Markdown::class, 'htmlDecode'));
    }

    public function test_height_method_exists(): void
    {
        $this->assertTrue(method_exists(Markdown::class, 'height'));
    }

    public function test_disk_method_exists(): void
    {
        $this->assertTrue(method_exists(Markdown::class, 'disk'));
    }

    public function test_image_directory_method_exists(): void
    {
        $this->assertTrue(method_exists(Markdown::class, 'imageDirectory'));
    }

    public function test_image_url_method_exists(): void
    {
        $this->assertTrue(method_exists(Markdown::class, 'imageUrl'));
    }

    public function test_language_url_method_exists(): void
    {
        $this->assertTrue(method_exists(Markdown::class, 'languageUrl'));
    }

    public function test_render_method_exists(): void
    {
        $this->assertTrue(method_exists(Markdown::class, 'render'));
    }

    // -------------------------------------------------------
    // Method visibility
    // -------------------------------------------------------

    public function test_html_decode_is_public(): void
    {
        $method = new \ReflectionMethod(Markdown::class, 'htmlDecode');
        $this->assertTrue($method->isPublic());
    }

    public function test_height_is_public(): void
    {
        $method = new \ReflectionMethod(Markdown::class, 'height');
        $this->assertTrue($method->isPublic());
    }

    public function test_disk_is_public(): void
    {
        $method = new \ReflectionMethod(Markdown::class, 'disk');
        $this->assertTrue($method->isPublic());
    }

    public function test_image_directory_is_public(): void
    {
        $method = new \ReflectionMethod(Markdown::class, 'imageDirectory');
        $this->assertTrue($method->isPublic());
    }

    public function test_image_url_is_public(): void
    {
        $method = new \ReflectionMethod(Markdown::class, 'imageUrl');
        $this->assertTrue($method->isPublic());
    }

    public function test_language_url_is_public(): void
    {
        $method = new \ReflectionMethod(Markdown::class, 'languageUrl');
        $this->assertTrue($method->isPublic());
    }

    public function test_render_is_public(): void
    {
        $method = new \ReflectionMethod(Markdown::class, 'render');
        $this->assertTrue($method->isPublic());
    }

    // -------------------------------------------------------
    // Protected methods
    // -------------------------------------------------------

    public function test_default_image_upload_url_is_protected(): void
    {
        $method = new \ReflectionMethod(Markdown::class, 'defaultImageUploadUrl');
        $this->assertTrue($method->isProtected());
    }

    public function test_format_url_is_protected(): void
    {
        $method = new \ReflectionMethod(Markdown::class, 'formatUrl');
        $this->assertTrue($method->isProtected());
    }

    public function test_require_lang_is_protected(): void
    {
        $method = new \ReflectionMethod(Markdown::class, 'requireLang');
        $this->assertTrue($method->isProtected());
    }

    // -------------------------------------------------------
    // Parameter checks
    // -------------------------------------------------------

    public function test_html_decode_has_decode_parameter(): void
    {
        $method = new \ReflectionMethod(Markdown::class, 'htmlDecode');
        $params = $method->getParameters();

        $this->assertCount(1, $params);
        $this->assertSame('decode', $params[0]->getName());
    }

    public function test_height_has_height_parameter(): void
    {
        $method = new \ReflectionMethod(Markdown::class, 'height');
        $params = $method->getParameters();

        $this->assertCount(1, $params);
        $this->assertSame('height', $params[0]->getName());
    }

    public function test_disk_has_string_parameter(): void
    {
        $method = new \ReflectionMethod(Markdown::class, 'disk');
        $params = $method->getParameters();

        $this->assertCount(1, $params);
        $this->assertSame('disk', $params[0]->getName());
    }

    public function test_image_directory_has_dir_parameter(): void
    {
        $method = new \ReflectionMethod(Markdown::class, 'imageDirectory');
        $params = $method->getParameters();

        $this->assertCount(1, $params);
        $this->assertSame('dir', $params[0]->getName());
    }

    public function test_default_langs_contains_en(): void
    {
        $reflection = new \ReflectionClass(Markdown::class);
        $defaults = $reflection->getDefaultProperties();

        $this->assertArrayHasKey('defaultLangs', $defaults);
        $this->assertArrayHasKey('en', $defaults['defaultLangs']);
    }

    public function test_default_langs_contains_zh_tw(): void
    {
        $reflection = new \ReflectionClass(Markdown::class);
        $defaults = $reflection->getDefaultProperties();

        $this->assertArrayHasKey('zh_TW', $defaults['defaultLangs']);
    }
}
