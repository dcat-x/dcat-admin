<?php

namespace Dcat\Admin\Tests\Unit\Actions;

use Dcat\Admin\Actions\Response;
use Dcat\Admin\Tests\TestCase;

class ResponseTest extends TestCase
{
    public function test_default_status_is_true(): void
    {
        $response = new Response;
        $array = $response->toArray();
        $this->assertTrue($array['status']);
    }

    public function test_status_sets_value(): void
    {
        $response = new Response;
        $result = $response->status(false);
        $this->assertSame($response, $result);
        $this->assertFalse($response->toArray()['status']);
    }

    public function test_message_sets_data(): void
    {
        $response = new Response;
        $result = $response->message('Hello');
        $this->assertSame($response, $result);
        $this->assertEquals('Hello', $response->toArray()['data']['message']);
    }

    public function test_success_sets_status_and_type(): void
    {
        $response = new Response;
        $result = $response->success('Done!');
        $this->assertSame($response, $result);
        $array = $response->toArray();
        $this->assertTrue($array['status']);
        $this->assertEquals('success', $array['data']['type']);
        $this->assertEquals('Done!', $array['data']['message']);
    }

    public function test_error_sets_status_false_and_type(): void
    {
        $response = new Response;
        $response->error('Failed!');
        $array = $response->toArray();
        $this->assertFalse($array['status']);
        $this->assertEquals('error', $array['data']['type']);
        $this->assertEquals('Failed!', $array['data']['message']);
    }

    public function test_info_sets_type(): void
    {
        $response = new Response;
        $response->info('Note');
        $this->assertEquals('info', $response->toArray()['data']['type']);
    }

    public function test_warning_sets_type(): void
    {
        $response = new Response;
        $response->warning('Careful');
        $this->assertEquals('warning', $response->toArray()['data']['type']);
    }

    public function test_timeout_sets_data(): void
    {
        $response = new Response;
        $response->timeout(5000);
        $this->assertEquals(5000, $response->toArray()['data']['timeout']);
    }

    public function test_alert_sets_data(): void
    {
        $response = new Response;
        $response->alert(true);
        $this->assertTrue($response->toArray()['data']['alert']);
    }

    public function test_detail_sets_data(): void
    {
        $response = new Response;
        $response->detail('Some detail');
        $this->assertEquals('Some detail', $response->toArray()['data']['detail']);
    }

    public function test_refresh_sets_then_action(): void
    {
        $response = new Response;
        $response->refresh();
        $then = $response->toArray()['data']['then'];
        $this->assertEquals('refresh', $then['action']);
        $this->assertTrue($then['value']);
    }

    public function test_script_sets_then_action(): void
    {
        $response = new Response;
        $response->script('alert(1)');
        $then = $response->toArray()['data']['then'];
        $this->assertEquals('script', $then['action']);
        $this->assertEquals('alert(1)', $then['value']);
    }

    public function test_data_merges_values(): void
    {
        $response = new Response;
        $response->data(['foo' => 'bar']);
        $response->data(['baz' => 'qux']);
        $data = $response->toArray()['data'];
        $this->assertEquals('bar', $data['foo']);
        $this->assertEquals('qux', $data['baz']);
    }

    public function test_html_sets_value(): void
    {
        $response = new Response;
        $response->html('<div>Test</div>');
        $array = $response->toArray();
        $this->assertEquals('<div>Test</div>', $array['html']);
    }

    public function test_options_merges_into_array(): void
    {
        $response = new Response;
        $response->options(['custom_key' => 'value']);
        $array = $response->toArray();
        $this->assertEquals('value', $array['custom_key']);
    }

    public function test_to_array_structure(): void
    {
        $response = new Response;
        $array = $response->toArray();
        $this->assertArrayHasKey('status', $array);
        $this->assertArrayHasKey('data', $array);
    }

    public function test_make_factory_method(): void
    {
        $response = Response::make(['initial' => 'data']);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals('data', $response->toArray()['data']['initial']);
    }

    public function test_status_code_sets_value(): void
    {
        $response = new Response;
        $result = $response->statusCode(422);
        $this->assertSame($response, $result);
        // statusCode is used in send(), verify via reflection
        $ref = new \ReflectionProperty($response, 'statusCode');
        $ref->setAccessible(true);
        $this->assertEquals(422, $ref->getValue($response));
    }

    public function test_chaining_methods(): void
    {
        $response = (new Response)
            ->success('Created')
            ->detail('Item created successfully')
            ->timeout(3000)
            ->refresh();

        $array = $response->toArray();
        $this->assertTrue($array['status']);
        $this->assertEquals('success', $array['data']['type']);
        $this->assertEquals('Item created successfully', $array['data']['detail']);
        $this->assertEquals(3000, $array['data']['timeout']);
        $this->assertEquals('refresh', $array['data']['then']['action']);
    }
}
