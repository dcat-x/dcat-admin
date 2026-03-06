<?php

namespace Dcat\Admin\Tests\Unit\Support;

use Dcat\Admin\Support\JavaScript;
use Dcat\Admin\Tests\TestCase;

class JavaScriptTest extends TestCase
{
    protected function tearDown(): void
    {
        // Clear static scripts registry between tests
        $ref = new \ReflectionProperty(JavaScript::class, 'scripts');
        $ref->setAccessible(true);
        $ref->setValue(null, []);

        parent::tearDown();
    }

    public function test_constructor_registers_script(): void
    {
        $js = new JavaScript('alert(1)');
        $all = JavaScript::all();
        $this->assertNotEmpty($all);
        $this->assertContains('alert(1)', $all);
    }

    public function test_value_getter_returns_script(): void
    {
        $js = new JavaScript('console.log("hello")');
        $this->assertEquals('console.log("hello")', $js->value());
    }

    public function test_value_setter_updates_script(): void
    {
        $js = new JavaScript('old');
        $js->value('new');
        $this->assertEquals('new', $js->value());
    }

    public function test_to_string_returns_id(): void
    {
        $js = new JavaScript('test');
        $id = (string) $js;
        $this->assertStringStartsWith('js(', $id);
        $this->assertStringEndsWith(')', $id);
    }

    public function test_make_returns_id_string(): void
    {
        $id = JavaScript::make('var x = 1;');
        $this->assertIsString($id);
        $this->assertStringStartsWith('js(', $id);
    }

    public function test_make_registers_script(): void
    {
        JavaScript::make('var x = 1;');
        $all = JavaScript::all();
        $this->assertNotEmpty($all);
        $this->assertContains('var x = 1;', $all);
    }

    public function test_all_returns_all_registered_scripts(): void
    {
        JavaScript::make('script1');
        JavaScript::make('script2');
        $all = JavaScript::all();
        $this->assertCount(2, $all);
        $values = array_values($all);
        $this->assertContains('script1', $values);
        $this->assertContains('script2', $values);
    }

    public function test_all_returns_empty_array_initially(): void
    {
        $this->assertEquals([], JavaScript::all());
    }

    public function test_delete_removes_script_by_id(): void
    {
        $js = new JavaScript('to_delete');
        $id = (string) $js;
        $this->assertContains($id, array_keys(JavaScript::all()));

        JavaScript::delete($id);
        $this->assertArrayNotHasKey($id, JavaScript::all());
    }

    public function test_format_replaces_id_in_json(): void
    {
        $id = JavaScript::make('function(){}');
        $data = ['callback' => $id];
        $result = JavaScript::format($data);
        $this->assertStringContainsString('function(){}', $result);
        $this->assertStringNotContainsString('js(', $result);
    }

    public function test_format_with_string_value(): void
    {
        $result = JavaScript::format('plain string');
        $this->assertEquals('plain string', $result);
    }

    public function test_format_with_array_without_js_ids(): void
    {
        $result = JavaScript::format(['key' => 'value']);
        $decoded = json_decode($result, true);
        $this->assertEquals(['key' => 'value'], $decoded);
    }

    public function test_multiple_scripts_in_format(): void
    {
        $id1 = JavaScript::make('callback1()');
        $id2 = JavaScript::make('callback2()');
        $data = ['a' => $id1, 'b' => $id2];
        $result = JavaScript::format($data);
        $this->assertStringContainsString('callback1()', $result);
        $this->assertStringContainsString('callback2()', $result);
    }
}
