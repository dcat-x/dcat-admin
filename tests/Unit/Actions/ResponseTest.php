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
        $this->assertSame('Hello', $response->toArray()['data']['message']);
    }

    public function test_success_sets_status_and_type(): void
    {
        $response = new Response;
        $result = $response->success('Done!');
        $this->assertSame($response, $result);
        $array = $response->toArray();
        $this->assertTrue($array['status']);
        $this->assertSame('success', $array['data']['type']);
        $this->assertSame('Done!', $array['data']['message']);
    }

    public function test_error_sets_status_false_and_type(): void
    {
        $response = new Response;
        $response->error('Failed!');
        $array = $response->toArray();
        $this->assertFalse($array['status']);
        $this->assertSame('error', $array['data']['type']);
        $this->assertSame('Failed!', $array['data']['message']);
    }

    public function test_info_sets_type(): void
    {
        $response = new Response;
        $response->info('Note');
        $this->assertSame('info', $response->toArray()['data']['type']);
    }

    public function test_warning_sets_type(): void
    {
        $response = new Response;
        $response->warning('Careful');
        $this->assertSame('warning', $response->toArray()['data']['type']);
    }

    public function test_timeout_sets_data(): void
    {
        $response = new Response;
        $response->timeout(5000);
        $this->assertSame(5000, $response->toArray()['data']['timeout']);
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
        $this->assertSame('Some detail', $response->toArray()['data']['detail']);
    }

    public function test_refresh_sets_then_action(): void
    {
        $response = new Response;
        $response->refresh();
        $then = $response->toArray()['data']['then'];
        $this->assertSame('refresh', $then['action']);
        $this->assertTrue($then['value']);
    }

    public function test_script_sets_then_action(): void
    {
        $response = new Response;
        $response->script('alert(1)');
        $then = $response->toArray()['data']['then'];
        $this->assertSame('script', $then['action']);
        $this->assertSame('alert(1)', $then['value']);
    }

    public function test_data_merges_values(): void
    {
        $response = new Response;
        $response->data(['foo' => 'bar']);
        $response->data(['baz' => 'qux']);
        $data = $response->toArray()['data'];
        $this->assertSame('bar', $data['foo']);
        $this->assertSame('qux', $data['baz']);
    }

    public function test_html_sets_value(): void
    {
        $response = new Response;
        $response->html('<div>Test</div>');
        $array = $response->toArray();
        $this->assertSame('<div>Test</div>', $array['html']);
    }

    public function test_options_merges_into_array(): void
    {
        $response = new Response;
        $response->options(['custom_key' => 'value']);
        $array = $response->toArray();
        $this->assertSame('value', $array['custom_key']);
    }

    public function test_to_array_structure(): void
    {
        $response = new Response;
        $array = $response->toArray();
        $this->assertIsBool($array['status'] ?? null);
        $this->assertIsArray($array['data'] ?? null);
    }

    public function test_make_factory_method(): void
    {
        $response = Response::make(['initial' => 'data']);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame('data', $response->toArray()['data']['initial']);
    }

    public function test_status_code_sets_value(): void
    {
        $response = new Response;
        $result = $response->statusCode(422);
        $this->assertSame($response, $result);
        // statusCode is used in send(), verify via reflection
        $ref = new \ReflectionProperty($response, 'statusCode');
        $ref->setAccessible(true);
        $this->assertSame(422, $ref->getValue($response));
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
        $this->assertSame('success', $array['data']['type']);
        $this->assertSame('Item created successfully', $array['data']['detail']);
        $this->assertSame(3000, $array['data']['timeout']);
        $this->assertSame('refresh', $array['data']['then']['action']);
    }
}
