<?php

namespace Dcat\Admin\Tests\Unit\Widgets;

use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Widgets\Code;

class CodeTest extends TestCase
{
    public function test_code_creation(): void
    {
        $code = new Code('echo "hello";');
        $this->assertInstanceOf(Code::class, $code);
    }

    public function test_code_with_string_content(): void
    {
        $code = new Code('echo "hello";');

        // Code extends Markdown, content is stored internally
        $reflection = new \ReflectionClass($code);
        $property = $reflection->getProperty('content');
        $property->setAccessible(true);

        $this->assertEquals('echo "hello";', $property->getValue($code));
    }

    public function test_code_with_array_content(): void
    {
        $data = ['key' => 'value', 'num' => 42];
        $code = new Code($data);

        $reflection = new \ReflectionClass($code);
        $property = $reflection->getProperty('content');
        $property->setAccessible(true);

        $expected = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $this->assertEquals($expected, $property->getValue($code));
    }

    public function test_code_with_object_content(): void
    {
        $obj = (object) ['name' => 'test'];
        $code = new Code($obj);

        $reflection = new \ReflectionClass($code);
        $property = $reflection->getProperty('content');
        $property->setAccessible(true);

        $expected = json_encode($obj, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $this->assertEquals($expected, $property->getValue($code));
    }

    public function test_code_default_lang_is_php(): void
    {
        $code = new Code('test');

        $reflection = new \ReflectionClass($code);
        $property = $reflection->getProperty('lang');
        $property->setAccessible(true);

        $this->assertEquals('php', $property->getValue($code));
    }

    public function test_code_lang_setter(): void
    {
        $code = new Code('test');
        $result = $code->lang('ruby');

        $this->assertSame($code, $result);

        $reflection = new \ReflectionClass($code);
        $property = $reflection->getProperty('lang');
        $property->setAccessible(true);

        $this->assertEquals('ruby', $property->getValue($code));
    }

    public function test_code_javascript_shortcut(): void
    {
        $code = new Code('var x = 1;');
        $code->javascript();

        $reflection = new \ReflectionClass($code);
        $property = $reflection->getProperty('lang');
        $property->setAccessible(true);

        $this->assertEquals('javascript', $property->getValue($code));
    }

    public function test_code_html_shortcut(): void
    {
        $code = new Code('<div>test</div>');
        $code->asHtml();

        $reflection = new \ReflectionClass($code);
        $property = $reflection->getProperty('lang');
        $property->setAccessible(true);

        $this->assertEquals('html', $property->getValue($code));
    }

    public function test_code_java_shortcut(): void
    {
        $code = new Code('System.out.println("hello");');
        $code->java();

        $reflection = new \ReflectionClass($code);
        $property = $reflection->getProperty('lang');
        $property->setAccessible(true);

        $this->assertEquals('java', $property->getValue($code));
    }

    public function test_code_python_shortcut(): void
    {
        $code = new Code('print("hello")');
        $code->python();

        $reflection = new \ReflectionClass($code);
        $property = $reflection->getProperty('lang');
        $property->setAccessible(true);

        $this->assertEquals('python', $property->getValue($code));
    }

    public function test_code_lang_chaining(): void
    {
        $code = new Code('test');
        $result = $code->javascript();

        $this->assertInstanceOf(Code::class, $result);
    }

    public function test_code_read_file_content(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'code_test_');
        file_put_contents($tmpFile, "line1\nline2\nline3\nline4\nline5\n");

        $code = new Code('dummy');
        $code->readFileContent($tmpFile, 2, 4);

        $reflection = new \ReflectionClass($code);
        $property = $reflection->getProperty('content');
        $property->setAccessible(true);

        $content = $property->getValue($code);
        $this->assertStringContainsString('line2', $content);
        $this->assertStringContainsString('line3', $content);
        $this->assertStringContainsString('line4', $content);
        $this->assertStringNotContainsString('line1', $content);
        $this->assertStringNotContainsString('line5', $content);

        unlink($tmpFile);
    }

    public function test_code_read_file_content_with_invalid_file(): void
    {
        $code = new Code('original');
        $result = $code->readFileContent('/nonexistent/file.txt', 1, 10);

        $this->assertInstanceOf(Code::class, $result);
    }

    public function test_code_read_file_content_with_end_less_than_start(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'code_test_');
        file_put_contents($tmpFile, "line1\nline2\n");

        $code = new Code('original');
        $result = $code->readFileContent($tmpFile, 5, 2);

        // end < start should return $this without reading
        $this->assertInstanceOf(Code::class, $result);

        unlink($tmpFile);
    }

    public function test_code_section_method(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'code_test_');
        $lines = [];
        for ($i = 1; $i <= 20; $i++) {
            $lines[] = "line{$i}";
        }
        file_put_contents($tmpFile, implode("\n", $lines)."\n");

        $code = new Code('dummy');
        // section reads lineNumber +/- context (default 5)
        $code->section($tmpFile, 10, 2);

        $reflection = new \ReflectionClass($code);
        $property = $reflection->getProperty('content');
        $property->setAccessible(true);

        $content = $property->getValue($code);
        // Should contain lines 8-12 (10-2 to 10+2)
        $this->assertStringContainsString('line8', $content);
        $this->assertStringContainsString('line10', $content);
        $this->assertStringContainsString('line12', $content);

        unlink($tmpFile);
    }

    public function test_code_render_content_wraps_with_fences(): void
    {
        $code = new Code('echo "test";');

        $reflection = new \ReflectionClass($code);
        $method = $reflection->getMethod('renderContent');
        $method->setAccessible(true);

        $output = $method->invoke($code);

        $this->assertStringContainsString('```php', $output);
        $this->assertStringContainsString('echo "test";', $output);
        $this->assertStringContainsString('```', $output);
    }

    public function test_code_render_content_with_custom_lang(): void
    {
        $code = new Code('var x = 1;');
        $code->javascript();

        $reflection = new \ReflectionClass($code);
        $method = $reflection->getMethod('renderContent');
        $method->setAccessible(true);

        $output = $method->invoke($code);

        $this->assertStringContainsString('```javascript', $output);
    }

    public function test_code_static_make(): void
    {
        $code = Code::make('test code');
        $this->assertInstanceOf(Code::class, $code);
    }

    public function test_code_constructor_with_file(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'code_test_');
        file_put_contents($tmpFile, "file content here\n");

        $code = new Code($tmpFile);

        $reflection = new \ReflectionClass($code);
        $property = $reflection->getProperty('content');
        $property->setAccessible(true);

        $content = $property->getValue($code);
        $this->assertStringContainsString('file content here', $content);

        unlink($tmpFile);
    }
}
