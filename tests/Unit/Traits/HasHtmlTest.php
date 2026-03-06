<?php

namespace Dcat\Admin\Tests\Unit\Traits;

use Dcat\Admin\Admin;
use Dcat\Admin\Tests\TestCase;

class HasHtmlTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Reset the html context before each test
        Admin::context()->html = null;
    }

    public function test_resolve_html_extracts_body_content(): void
    {
        $html = '<div>Hello World</div>';

        $result = Admin::resolveHtml($html, ['runScript' => false]);

        $this->assertIsArray($result);
        $this->assertIsString($result['script'] ?? null);
        $this->assertStringContainsString('Hello World', $result['html'] ?? '');
    }

    public function test_resolve_html_extracts_scripts(): void
    {
        $html = '<div>Content</div><script>alert("hi")</script>';

        $result = Admin::resolveHtml($html, ['runScript' => false]);

        $this->assertNotEmpty($result['script']);
        $this->assertStringContainsString('alert("hi")', $result['script']);
    }

    public function test_resolve_html_handles_empty_string(): void
    {
        $result = Admin::resolveHtml('', ['runScript' => false]);

        $this->assertIsArray($result);
        $this->assertSame('', $result['script']);
    }

    public function test_resolve_html_extracts_style_tags(): void
    {
        $html = '<style>.test { color: red; }</style><div>Content</div>';

        $result = Admin::resolveHtml($html, ['runScript' => false]);

        // Style content should be extracted (not in html output)
        $this->assertStringNotContainsString('<style>', $result['html']);
    }

    public function test_get_dom_document_returns_dom(): void
    {
        $reflection = new \ReflectionMethod(Admin::class, 'getDOMDocument');

        $dom = $reflection->invoke(null, '<div>test</div>');

        $this->assertInstanceOf(\DOMDocument::class, $dom);
    }

    public function test_resolve_element_returns_empty_for_null(): void
    {
        $reflection = new \ReflectionMethod(Admin::class, 'resolveElement');

        $result = $reflection->invoke(null, null);

        $this->assertSame(['html' => '', 'script' => ''], $result);
    }

    public function test_should_resolve_tags_contains_expected(): void
    {
        $reflection = new \ReflectionProperty(Admin::class, 'shouldResolveTags');

        $tags = $reflection->getValue();

        $this->assertContains('style', $tags);
        $this->assertContains('script', $tags);
        $this->assertContains('template', $tags);
        $this->assertContains('link', $tags);
    }
}
