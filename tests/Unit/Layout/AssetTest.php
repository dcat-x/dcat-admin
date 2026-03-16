<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Layout;

use Dcat\Admin\Layout\Asset;
use Dcat\Admin\Tests\TestCase;

class AssetTest extends TestCase
{
    private Asset $asset;

    protected function setUp(): void
    {
        parent::setUp();
        $this->asset = new Asset;
    }

    public function test_css_adds_single_stylesheet(): void
    {
        $this->asset->css('custom.css');

        $this->assertContains('custom.css', $this->asset->css);
    }

    public function test_css_adds_array_of_stylesheets(): void
    {
        $this->asset->css(['first.css', 'second.css']);

        $this->assertContains('first.css', $this->asset->css);
        $this->assertContains('second.css', $this->asset->css);
    }

    public function test_css_ignores_falsy_value(): void
    {
        $initialCount = count($this->asset->css);

        $this->asset->css(null);
        $this->asset->css('');

        $this->assertCount($initialCount, $this->asset->css);
    }

    public function test_js_adds_single_script(): void
    {
        $this->asset->js('custom.js');

        $this->assertContains('custom.js', $this->asset->js);
    }

    public function test_js_adds_array_of_scripts(): void
    {
        $this->asset->js(['first.js', 'second.js']);

        $this->assertContains('first.js', $this->asset->js);
        $this->assertContains('second.js', $this->asset->js);
    }

    public function test_js_ignores_falsy_value(): void
    {
        $initialCount = count($this->asset->js);

        $this->asset->js(null);
        $this->asset->js('');

        $this->assertCount($initialCount, $this->asset->js);
    }

    public function test_script_adds_inline_script(): void
    {
        $this->asset->script('console.log("hello")');

        $this->assertContains('console.log("hello")', $this->asset->script);
    }

    public function test_script_adds_direct_script(): void
    {
        $this->asset->script('alert("direct")', true);

        $this->assertContains('alert("direct")', $this->asset->directScript);
        $this->assertNotContains('alert("direct")', $this->asset->script);
    }

    public function test_script_ignores_falsy_value(): void
    {
        $initialCount = count($this->asset->script);

        $this->asset->script(null);
        $this->asset->script('');

        $this->assertCount($initialCount, $this->asset->script);
    }

    public function test_style_adds_inline_css(): void
    {
        $this->asset->style('.red { color: red; }');

        $this->assertContains('.red { color: red; }', $this->asset->style);
    }

    public function test_style_ignores_falsy_value(): void
    {
        $initialCount = count($this->asset->style);

        $this->asset->style(null);
        $this->asset->style('');

        $this->assertCount($initialCount, $this->asset->style);
    }

    public function test_alias_set_and_get(): void
    {
        $this->asset->alias('@custom', [
            'js' => 'custom.js',
            'css' => 'custom.css',
        ]);

        $result = $this->asset->getAlias('@custom');

        $this->assertSame(['custom.js'], $result['js']);
        $this->assertSame(['custom.css'], $result['css']);
    }

    public function test_alias_auto_prepends_at_sign(): void
    {
        $this->asset->alias('mylib', [
            'js' => 'mylib.js',
        ]);

        $result = $this->asset->getAlias('mylib');

        $this->assertSame(['mylib.js'], $result['js']);
    }

    public function test_alias_with_array_sets_multiple(): void
    {
        $this->asset->alias([
            '@lib1' => ['js' => 'lib1.js'],
            '@lib2' => ['js' => 'lib2.js'],
        ]);

        $this->assertSame(['lib1.js'], $this->asset->getAlias('@lib1')['js']);
        $this->assertSame(['lib2.js'], $this->asset->getAlias('@lib2')['js']);
    }

    public function test_get_alias_returns_path_string_for_path_alias(): void
    {
        $result = $this->asset->getAlias('@admin');

        $this->assertSame('vendor/dcat-admin', $result);
    }

    public function test_get_alias_returns_empty_for_nonexistent(): void
    {
        $result = $this->asset->getAlias('@nonexistent');

        $this->assertSame(['js' => null, 'css' => null], $result);
    }

    public function test_has_alias_returns_true_for_existing(): void
    {
        $this->assertTrue($this->asset->hasAlias('@admin'));
        $this->assertTrue($this->asset->hasAlias('@dcat'));
    }

    public function test_has_alias_returns_false_for_nonexistent(): void
    {
        $this->assertFalse($this->asset->hasAlias('@nonexistent'));
    }

    public function test_is_path_alias_returns_true_for_string_alias(): void
    {
        $this->assertTrue($this->asset->isPathAlias('@admin'));
    }

    public function test_is_path_alias_returns_false_for_array_alias(): void
    {
        $this->assertFalse($this->asset->isPathAlias('@dcat'));
    }

    public function test_is_path_alias_returns_false_for_nonexistent(): void
    {
        $this->assertFalse($this->asset->isPathAlias('@nonexistent'));
    }

    public function test_get_real_path_resolves_path_alias(): void
    {
        $result = $this->asset->getRealPath('@admin/js/app.js');

        $this->assertSame('vendor/dcat-admin/js/app.js', $result);
    }

    public function test_get_real_path_returns_plain_path_unchanged(): void
    {
        $result = $this->asset->getRealPath('plain/path.js');

        $this->assertSame('plain/path.js', $result);
    }

    public function test_get_real_path_handles_null(): void
    {
        $result = $this->asset->getRealPath(null);

        $this->assertNull($result);
    }

    public function test_base_css_replaces_when_merge_false(): void
    {
        $this->asset->baseCss(['custom' => '@custom']);

        $this->assertSame(['custom' => '@custom'], $this->asset->baseCss);
    }

    public function test_base_css_merges_when_merge_true(): void
    {
        $original = $this->asset->baseCss;

        $this->asset->baseCss(['extra' => '@extra'], true);

        $this->assertMergedAssetsContainOriginalKeys($original, $this->asset->baseCss, 'extra');
    }

    public function test_base_js_replaces_when_merge_false(): void
    {
        $this->asset->baseJs(['only' => '@only'], false);

        $this->assertSame(['only' => '@only'], $this->asset->baseJs);
    }

    public function test_base_js_merges_when_merge_true(): void
    {
        $original = $this->asset->baseJs;

        $this->asset->baseJs(['extra' => '@extra'], true);

        $this->assertMergedAssetsContainOriginalKeys($original, $this->asset->baseJs, 'extra');
    }

    public function test_header_js_merges_by_default(): void
    {
        $original = $this->asset->headerJs;

        $this->asset->headerJs(['extra' => '@extra']);

        $this->assertMergedAssetsContainOriginalKeys($original, $this->asset->headerJs, 'extra');
    }

    public function test_header_js_replaces_when_merge_false(): void
    {
        $this->asset->headerJs(['only' => '@only'], false);

        $this->assertSame(['only' => '@only'], $this->asset->headerJs);
    }

    public function test_require_adds_js_and_css_from_alias(): void
    {
        $this->asset->alias('@testlib', [
            'js' => 'testlib.js',
            'css' => 'testlib.css',
        ]);

        $this->asset->require('@testlib');

        $this->assertContains('testlib.js', $this->asset->js);
        $this->assertContains('testlib.css', $this->asset->css);
    }

    public function test_require_array_of_aliases(): void
    {
        $this->asset->alias('@lib_a', [
            'js' => 'a.js',
        ]);
        $this->asset->alias('@lib_b', [
            'css' => 'b.css',
        ]);

        $this->asset->require(['@lib_a', '@lib_b']);

        $this->assertContains('a.js', $this->asset->js);
        $this->assertContains('b.css', $this->asset->css);
    }

    public function test_script_to_html_produces_script_tag(): void
    {
        $this->asset->script('console.log("test")');

        $html = $this->asset->scriptToHtml();

        $this->assertStringContainsString('<script', $html);
        $this->assertStringContainsString('console.log("test")', $html);
        $this->assertStringContainsString('Dcat.ready', $html);
    }

    public function test_script_to_html_with_direct_script(): void
    {
        $this->asset->script('directCode()', true);

        $html = $this->asset->scriptToHtml();

        $this->assertStringContainsString('directCode()', $html);
    }

    public function test_style_to_html_produces_style_tag(): void
    {
        $this->asset->style('.test { color: blue; }');

        $html = $this->asset->styleToHtml();

        $this->assertStringContainsString('<style>', $html);
        $this->assertStringContainsString('.test { color: blue; }', $html);
        $this->assertStringContainsString('</style>', $html);
    }

    public function test_with_version_query_appends_version(): void
    {
        $result = $this->asset->withVersionQuery('http://example.com/app.js');

        $this->assertStringContainsString('?v', $result);
    }

    public function test_with_version_query_with_existing_query_params(): void
    {
        $result = $this->asset->withVersionQuery('http://example.com/app.js?foo=bar');

        $this->assertStringContainsString('&v', $result);
    }

    private function assertMergedAssetsContainOriginalKeys(array $original, array $merged, string $extraKey): void
    {
        $mergedKeys = array_keys($merged);
        $this->assertContains($extraKey, $mergedKeys);

        foreach (array_keys($original) as $key) {
            $this->assertContains($key, $mergedKeys);
        }
    }
}
