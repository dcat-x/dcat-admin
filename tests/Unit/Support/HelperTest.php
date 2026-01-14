<?php

namespace Dcat\Admin\Tests\Unit\Support;

use Dcat\Admin\Support\Helper;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Support\Collection;

class HelperTest extends TestCase
{
    public function test_array_with_null(): void
    {
        $this->assertEquals([], Helper::array(null));
    }

    public function test_array_with_empty_string(): void
    {
        $this->assertEquals([], Helper::array(''));
    }

    public function test_array_with_empty_array(): void
    {
        $this->assertEquals([], Helper::array([]));
    }

    public function test_array_with_array(): void
    {
        $input = ['a', 'b', 'c'];
        $this->assertEquals(['a', 'b', 'c'], Helper::array($input));
    }

    public function test_array_with_comma_separated_string(): void
    {
        $this->assertEquals(['a', 'b', 'c'], Helper::array('a,b,c'));
    }

    public function test_array_with_json_string(): void
    {
        $this->assertEquals(['a', 'b', 'c'], Helper::array('["a","b","c"]'));
    }

    public function test_array_with_closure(): void
    {
        $result = Helper::array(function () {
            return ['a', 'b'];
        });
        $this->assertEquals(['a', 'b'], $result);
    }

    public function test_array_with_collection(): void
    {
        $collection = new Collection(['a', 'b', 'c']);
        $this->assertEquals(['a', 'b', 'c'], Helper::array($collection));
    }

    public function test_array_filter_empty_values(): void
    {
        $input = ['a', '', null, 'b'];
        $result = Helper::array($input, true);
        $this->assertEquals(['a', 'b'], array_values($result));
    }

    public function test_array_without_filter(): void
    {
        $input = ['a', '', null, 'b'];
        $result = Helper::array($input, false);
        $this->assertCount(4, $result);
    }

    public function test_render_string(): void
    {
        $this->assertEquals('hello', Helper::render('hello'));
    }

    public function test_render_closure(): void
    {
        $result = Helper::render(function () {
            return 'from closure';
        });
        $this->assertEquals('from closure', $result);
    }

    public function test_render_closure_with_params(): void
    {
        $result = Helper::render(function ($a, $b) {
            return $a.$b;
        }, ['hello', 'world']);
        $this->assertEquals('helloworld', $result);
    }

    public function test_url_with_query(): void
    {
        $url = 'http://example.com/path';
        $result = Helper::urlWithQuery($url, ['foo' => 'bar']);
        $this->assertEquals('http://example.com/path?foo=bar', $result);
    }

    public function test_url_with_query_merge_existing(): void
    {
        $url = 'http://example.com/path?existing=value';
        $result = Helper::urlWithQuery($url, ['foo' => 'bar']);
        $this->assertStringContainsString('existing=value', $result);
        $this->assertStringContainsString('foo=bar', $result);
    }

    public function test_url_with_query_empty_query(): void
    {
        $url = 'http://example.com/path';
        $result = Helper::urlWithQuery($url, []);
        $this->assertEquals('http://example.com/path', $result);
    }

    public function test_url_without_query(): void
    {
        $url = 'http://example.com/path?foo=bar&baz=qux';
        $result = Helper::urlWithoutQuery($url, 'foo');
        $this->assertEquals('http://example.com/path?baz=qux', $result);
    }

    public function test_url_without_query_multiple_keys(): void
    {
        $url = 'http://example.com/path?foo=bar&baz=qux&test=value';
        $result = Helper::urlWithoutQuery($url, ['foo', 'baz']);
        $this->assertEquals('http://example.com/path?test=value', $result);
    }

    public function test_url_without_query_no_query_string(): void
    {
        $url = 'http://example.com/path';
        $result = Helper::urlWithoutQuery($url, 'foo');
        $this->assertEquals('http://example.com/path', $result);
    }

    public function test_url_has_query(): void
    {
        $url = 'http://example.com/path?foo=bar';
        $this->assertTrue(Helper::urlHasQuery($url, 'foo'));
        $this->assertFalse(Helper::urlHasQuery($url, 'baz'));
    }

    public function test_url_has_query_multiple_keys(): void
    {
        $url = 'http://example.com/path?foo=bar&baz=qux';
        $this->assertTrue(Helper::urlHasQuery($url, ['foo', 'other']));
        $this->assertFalse(Helper::urlHasQuery($url, ['other', 'another']));
    }

    public function test_slug(): void
    {
        $this->assertEquals('user-name', Helper::slug('UserName'));
        $this->assertEquals('user-name', Helper::slug('userName'));
        $this->assertEquals('user-name', Helper::slug('user_name'));
    }

    public function test_slug_with_custom_symbol(): void
    {
        $this->assertEquals('user_name', Helper::slug('UserName', '_'));
    }

    public function test_build_nested_array(): void
    {
        $nodes = [
            ['id' => 1, 'parent_id' => 0, 'name' => 'Root'],
            ['id' => 2, 'parent_id' => 1, 'name' => 'Child 1'],
            ['id' => 3, 'parent_id' => 1, 'name' => 'Child 2'],
            ['id' => 4, 'parent_id' => 2, 'name' => 'Grandchild'],
        ];

        $result = Helper::buildNestedArray($nodes);

        $this->assertCount(1, $result);
        $this->assertEquals('Root', $result[0]['name']);
        $this->assertCount(2, $result[0]['children']);
        $this->assertEquals('Child 1', $result[0]['children'][0]['name']);
        $this->assertCount(1, $result[0]['children'][0]['children']);
    }

    public function test_build_nested_array_custom_keys(): void
    {
        $nodes = [
            ['pk' => 1, 'pid' => 0, 'name' => 'Root'],
            ['pk' => 2, 'pid' => 1, 'name' => 'Child'],
        ];

        $result = Helper::buildNestedArray($nodes, 0, 'pk', 'pid', 'items');

        $this->assertCount(1, $result);
        $this->assertEquals('Root', $result[0]['name']);
        $this->assertArrayHasKey('items', $result[0]);
    }

    public function test_delete_by_value(): void
    {
        $array = ['a', 'b', 'c', 'd'];
        Helper::deleteByValue($array, 'b');
        $this->assertEquals(['a', 'c', 'd'], array_values($array));
    }

    public function test_delete_by_value_multiple(): void
    {
        $array = ['a', 'b', 'c', 'd'];
        Helper::deleteByValue($array, ['b', 'd']);
        $this->assertEquals(['a', 'c'], array_values($array));
    }

    public function test_delete_contains(): void
    {
        $array = ['foo', 'foobar', 'baz', 'qux'];
        Helper::deleteContains($array, 'bar');
        $this->assertEquals(['foo', 'baz', 'qux'], array_values($array));
    }

    public function test_color_to_rbg(): void
    {
        $result = Helper::colorToRBG('ffffff');
        $this->assertEquals([255, 255, 255], $result);

        $result = Helper::colorToRBG('000000');
        $this->assertEquals([0, 0, 0], $result);
    }

    public function test_color_lighten(): void
    {
        $result = Helper::colorLighten('#333333', 10);
        $this->assertIsString($result);
        $this->assertStringStartsWith('#', $result);
    }

    public function test_color_darken(): void
    {
        $result = Helper::colorDarken('#cccccc', 10);
        $this->assertIsString($result);
        $this->assertStringStartsWith('#', $result);
    }

    public function test_color_alpha(): void
    {
        $result = Helper::colorAlpha('#ffffff', 0.5);
        $this->assertStringContainsString('rgba', $result);
        $this->assertStringContainsString('0.5', $result);
    }

    public function test_color_alpha_no_change(): void
    {
        $result = Helper::colorAlpha('#ffffff', 1);
        $this->assertEquals('#ffffff', $result);
    }

    public function test_validate_extension_name(): void
    {
        $this->assertEquals(1, Helper::validateExtensionName('vendor/package'));
        $this->assertEquals(1, Helper::validateExtensionName('my-vendor/my-package'));
        $this->assertEquals(0, Helper::validateExtensionName('invalid'));
        $this->assertEquals(0, Helper::validateExtensionName('vendor/'));
    }

    public function test_get_file_icon(): void
    {
        $this->assertEquals('fa fa-file-image-o', Helper::getFileIcon('photo.jpg'));
        $this->assertEquals('fa fa-file-image-o', Helper::getFileIcon('photo.png'));
        $this->assertEquals('fa fa-file-pdf-o', Helper::getFileIcon('document.pdf'));
        $this->assertEquals('fa fa-file-word-o', Helper::getFileIcon('document.doc'));
        $this->assertEquals('fa fa-file-excel-o', Helper::getFileIcon('data.xlsx'));
        $this->assertEquals('fa fa-file-code-o', Helper::getFileIcon('script.php'));
        $this->assertEquals('fa fa-file-o', Helper::getFileIcon('unknown.xyz'));
    }

    public function test_equal(): void
    {
        $this->assertTrue(Helper::equal('1', 1));
        $this->assertTrue(Helper::equal(1, 1));
        $this->assertTrue(Helper::equal('test', 'test'));
        $this->assertFalse(Helper::equal(null, 1));
        $this->assertFalse(Helper::equal(1, null));
        $this->assertFalse(Helper::equal(1, 2));
    }

    public function test_in_array(): void
    {
        $this->assertTrue(Helper::inArray(1, [1, 2, 3]));
        $this->assertTrue(Helper::inArray('1', [1, 2, 3]));
        $this->assertTrue(Helper::inArray(1, ['1', '2', '3']));
        $this->assertFalse(Helper::inArray(4, [1, 2, 3]));
    }

    public function test_str_limit(): void
    {
        $this->assertEquals('hello', Helper::strLimit('hello', 10));
        $this->assertEquals('hel...', Helper::strLimit('hello', 3));
        $this->assertEquals('hel##', Helper::strLimit('hello', 3, '##'));
    }

    public function test_str_limit_unicode(): void
    {
        $this->assertEquals('你好...', Helper::strLimit('你好世界', 2));
    }

    public function test_format_element_name(): void
    {
        $this->assertEquals('name', Helper::formatElementName('name'));
        $this->assertEquals('user[name]', Helper::formatElementName('user.name'));
        $this->assertEquals('user[profile][name]', Helper::formatElementName('user.profile.name'));
    }

    public function test_format_element_name_array(): void
    {
        $result = Helper::formatElementName(['user.name', 'user.email']);
        $this->assertEquals(['user[name]', 'user[email]'], $result);
    }

    public function test_build_html_attributes(): void
    {
        $attributes = ['class' => 'btn', 'id' => 'submit'];
        $result = Helper::buildHtmlAttributes($attributes);
        $this->assertStringContainsString('class="btn"', $result);
        $this->assertStringContainsString('id="submit"', $result);
    }

    public function test_build_html_attributes_with_array_value(): void
    {
        $attributes = ['class' => ['btn', 'btn-primary']];
        $result = Helper::buildHtmlAttributes($attributes);
        $this->assertStringContainsString('class="btn btn-primary"', $result);
    }

    public function test_html_entity_encode(): void
    {
        $result = Helper::htmlEntityEncode('<script>alert("xss")</script>');
        $this->assertStringNotContainsString('<script>', $result);
        $this->assertStringContainsString('&lt;script&gt;', $result);
    }

    public function test_html_entity_encode_array(): void
    {
        $input = ['name' => '<b>test</b>', 'desc' => '<script>'];
        $result = Helper::htmlEntityEncode($input);
        $this->assertStringNotContainsString('<b>', $result['name']);
        $this->assertStringNotContainsString('<script>', $result['desc']);
    }

    public function test_basename(): void
    {
        $this->assertEquals('file.txt', Helper::basename('/path/to/file.txt'));
        $this->assertEquals('file.txt', Helper::basename('path/to/file.txt'));
        $this->assertEquals('file.txt', Helper::basename('file.txt'));
    }

    public function test_key_exists(): void
    {
        $array = ['foo' => 'bar', 'baz' => null];
        $this->assertTrue(Helper::keyExists('foo', $array));
        $this->assertTrue(Helper::keyExists('baz', $array));
        $this->assertFalse(Helper::keyExists('qux', $array));
    }

    public function test_array_set(): void
    {
        $array = [];
        Helper::arraySet($array, 'user.name', 'John');
        $this->assertEquals('John', $array['user']['name']);

        Helper::arraySet($array, 'user.email', 'john@example.com');
        $this->assertEquals('john@example.com', $array['user']['email']);
    }

    public function test_camel_array(): void
    {
        $array = ['user_name' => 'John', 'first_name' => 'Jane'];
        Helper::camelArray($array);
        $this->assertArrayHasKey('userName', $array);
        $this->assertArrayHasKey('firstName', $array);
    }

    public function test_export_array(): void
    {
        $array = ['foo' => 'bar', 'baz' => 123];
        $result = Helper::exportArray($array);
        $this->assertStringContainsString("'foo' => 'bar'", $result);
        $this->assertStringContainsString("'baz' => 123", $result);
    }

    public function test_export_array_with_boolean(): void
    {
        $array = ['enabled' => true, 'disabled' => false, 'empty' => null];
        $result = Helper::exportArray($array);
        $this->assertStringContainsString("'enabled' => true", $result);
        $this->assertStringContainsString("'disabled' => false", $result);
        $this->assertStringContainsString("'empty' => null", $result);
    }

    public function test_export_array_php(): void
    {
        $array = ['foo' => 'bar'];
        $result = Helper::exportArrayPhp($array);
        $this->assertStringStartsWith('<?php', $result);
        $this->assertStringContainsString('return [', $result);
    }
}
