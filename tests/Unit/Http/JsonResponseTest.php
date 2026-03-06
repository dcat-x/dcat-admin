<?php

namespace Dcat\Admin\Tests\Unit\Http;

use Dcat\Admin\Exception\AdminException;
use Dcat\Admin\Http\JsonResponse;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Support\MessageBag;
use Mockery;

class JsonResponseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_make_creates_new_instance(): void
    {
        $response = JsonResponse::make();

        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    public function test_make_with_initial_data(): void
    {
        $response = JsonResponse::make(['key' => 'value']);

        $array = $response->toArray();
        $this->assertSame('value', $array['data']['key']);
    }

    public function test_default_status_is_true(): void
    {
        $response = new JsonResponse;

        $array = $response->toArray();
        $this->assertTrue($array['status']);
    }

    public function test_status_can_be_set_to_false(): void
    {
        $response = new JsonResponse;
        $result = $response->status(false);

        $this->assertSame($response, $result);
        $this->assertFalse($response->toArray()['status']);
    }

    public function test_status_code_defaults_to_200(): void
    {
        $response = new JsonResponse;

        $ref = new \ReflectionProperty($response, 'statusCode');
        $ref->setAccessible(true);

        $this->assertSame(200, $ref->getValue($response));
    }

    public function test_status_code_can_be_changed(): void
    {
        $response = new JsonResponse;
        $result = $response->statusCode(422);

        $this->assertSame($response, $result);

        $ref = new \ReflectionProperty($response, 'statusCode');
        $ref->setAccessible(true);
        $this->assertSame(422, $ref->getValue($response));
    }

    public function test_message_sets_message_in_data(): void
    {
        $response = new JsonResponse;
        $result = $response->message('Test message');

        $this->assertSame($response, $result);
        $this->assertSame('Test message', $response->toArray()['data']['message']);
    }

    public function test_success_sets_status_true_and_type_success(): void
    {
        $response = new JsonResponse;
        $result = $response->success('Operation succeeded');

        $this->assertSame($response, $result);

        $array = $response->toArray();
        $this->assertTrue($array['status']);
        $this->assertSame('success', $array['data']['type']);
        $this->assertSame('Operation succeeded', $array['data']['message']);
    }

    public function test_error_sets_status_false_and_type_error(): void
    {
        $response = new JsonResponse;
        $result = $response->error('Something failed');

        $this->assertSame($response, $result);

        $array = $response->toArray();
        $this->assertFalse($array['status']);
        $this->assertSame('error', $array['data']['type']);
        $this->assertSame('Something failed', $array['data']['message']);
    }

    public function test_info_sets_type_info(): void
    {
        $response = new JsonResponse;
        $response->info('Info message');

        $array = $response->toArray();
        $this->assertSame('info', $array['data']['type']);
        $this->assertSame('Info message', $array['data']['message']);
    }

    public function test_warning_sets_type_warning(): void
    {
        $response = new JsonResponse;
        $response->warning('Warning message');

        $array = $response->toArray();
        $this->assertSame('warning', $array['data']['type']);
        $this->assertSame('Warning message', $array['data']['message']);
    }

    public function test_timeout_sets_timeout_in_data(): void
    {
        $response = new JsonResponse;
        $result = $response->timeout(5);

        $this->assertSame($response, $result);
        $this->assertSame(5, $response->toArray()['data']['timeout']);
    }

    public function test_alert_sets_alert_flag(): void
    {
        $response = new JsonResponse;
        $result = $response->alert();

        $this->assertSame($response, $result);
        $this->assertTrue($response->toArray()['data']['alert']);
    }

    public function test_alert_can_be_set_to_false(): void
    {
        $response = new JsonResponse;
        $response->alert(false);

        $this->assertFalse($response->toArray()['data']['alert']);
    }

    public function test_detail_sets_detail_in_data(): void
    {
        $response = new JsonResponse;
        $result = $response->detail('Some details here');

        $this->assertSame($response, $result);
        $this->assertSame('Some details here', $response->toArray()['data']['detail']);
    }

    public function test_refresh_sets_then_action_refresh(): void
    {
        $response = new JsonResponse;
        $result = $response->refresh();

        $this->assertSame($response, $result);

        $array = $response->toArray();
        $this->assertSame('refresh', $array['data']['then']['action']);
        $this->assertTrue($array['data']['then']['value']);
    }

    public function test_script_sets_then_action_script(): void
    {
        $response = new JsonResponse;
        $response->script('alert("hello")');

        $array = $response->toArray();
        $this->assertSame('script', $array['data']['then']['action']);
        $this->assertSame('alert("hello")', $array['data']['then']['value']);
    }

    public function test_data_merges_into_existing_data(): void
    {
        $response = new JsonResponse(['key1' => 'val1']);
        $response->data(['key2' => 'val2']);

        $array = $response->toArray();
        $this->assertSame('val1', $array['data']['key1']);
        $this->assertSame('val2', $array['data']['key2']);
    }

    public function test_html_sets_html_property(): void
    {
        $response = new JsonResponse;
        $result = $response->html('<div>Hello</div>');

        $this->assertSame($response, $result);

        $array = $response->toArray();
        $this->assertSame('<div>Hello</div>', $array['html'] ?? null);
    }

    public function test_options_merges_into_options(): void
    {
        $response = new JsonResponse;
        $response->options(['custom_key' => 'custom_value']);

        $array = $response->toArray();
        $this->assertSame('custom_value', $array['custom_key']);
    }

    public function test_with_validation_sets_errors_and_status(): void
    {
        $response = new JsonResponse;
        $errors = ['name' => ['Name is required']];
        $result = $response->withValidation($errors);

        $this->assertSame($response, $result);
        $this->assertFalse($response->toArray()['status']);

        $ref = new \ReflectionProperty($response, 'statusCode');
        $ref->setAccessible(true);
        $this->assertSame(422, $ref->getValue($response));

        $array = $response->toArray();
        $this->assertSame($errors, $array['errors']);
    }

    public function test_with_validation_accepts_message_bag(): void
    {
        $response = new JsonResponse;
        $messageBag = new MessageBag(['email' => ['Invalid email']]);
        $response->withValidation($messageBag);

        $array = $response->toArray();
        $this->assertFalse($array['status']);
        $this->assertSame(['Invalid email'], $array['errors']['email'] ?? null);
    }

    public function test_with_exception_sets_error_status(): void
    {
        $response = new JsonResponse;
        $exception = new \RuntimeException('Something went wrong');
        $result = $response->withException($exception);

        $this->assertSame($response, $result);

        $array = $response->toArray();
        $this->assertFalse($array['status']);
        $this->assertSame('error', $array['data']['type']);
        $this->assertStringContainsString('RuntimeException', $array['data']['message']);
        $this->assertStringContainsString('Something went wrong', $array['data']['message']);
    }

    public function test_to_array_returns_correct_structure(): void
    {
        $response = new JsonResponse;
        $response->success('Done');

        $array = $response->toArray();

        $this->assertIsArray($array['data'] ?? null);
        $this->assertTrue($array['status']);
    }

    public function test_send_returns_illuminate_json_response(): void
    {
        $response = new JsonResponse;
        $response->success('Done');

        $result = $response->send();

        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $result);
        $this->assertSame(200, $result->getStatusCode());
    }

    public function test_send_with_custom_status_code(): void
    {
        $response = new JsonResponse;
        $response->error('Not Found')->statusCode(404);

        $result = $response->send();

        $this->assertSame(404, $result->getStatusCode());
    }

    public function test_conditional_if_methods_call_method_when_true(): void
    {
        $response = new JsonResponse;
        $response->successIf(true, 'Conditional success');

        $array = $response->toArray();
        $this->assertTrue($array['status']);
        $this->assertSame('success', $array['data']['type']);
        $this->assertSame('Conditional success', $array['data']['message']);
    }

    public function test_conditional_if_methods_skip_when_false(): void
    {
        $response = new JsonResponse;
        $response->errorIf(false, 'Should not appear');

        $array = $response->toArray();
        // Status should remain true (default) because errorIf condition was false
        $this->assertTrue($array['status']);
        $this->assertArrayNotHasKey('type', $array['data']);
    }

    public function test_calling_undefined_method_throws_exception(): void
    {
        $response = new JsonResponse;

        $this->expectException(AdminException::class);
        $this->expectExceptionMessage('Call to undefined method "nonExistentMethod"');

        $response->nonExistentMethod();
    }

    public function test_implements_arrayable(): void
    {
        $response = new JsonResponse;

        $this->assertInstanceOf(\Illuminate\Contracts\Support\Arrayable::class, $response);
    }

    public function test_redirect_sets_then_action_redirect(): void
    {
        $response = new JsonResponse;
        $response->redirect('/dashboard');

        $array = $response->toArray();
        $this->assertSame('redirect', $array['data']['then']['action']);
        $this->assertNotEmpty($array['data']['then']['value']);
    }

    public function test_location_sets_then_action_location(): void
    {
        $response = new JsonResponse;
        $response->location('/users');

        $array = $response->toArray();
        $this->assertSame('location', $array['data']['then']['action']);
    }

    public function test_location_without_url_sets_null_value(): void
    {
        $response = new JsonResponse;
        $response->location();

        $array = $response->toArray();
        $this->assertSame('location', $array['data']['then']['action']);
        $this->assertNull($array['data']['then']['value']);
    }
}
