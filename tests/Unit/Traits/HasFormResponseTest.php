<?php

namespace Dcat\Admin\Tests\Unit\Traits;

use Dcat\Admin\Http\JsonResponse;
use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Traits\HasFormResponse;

class HasFormResponseTest extends TestCase
{
    protected function createTraitUser(): object
    {
        return new class
        {
            use HasFormResponse;

            const CURRENT_URL_NAME = '_current_';
        };
    }

    public function test_response_returns_json_response(): void
    {
        $user = $this->createTraitUser();
        $response = $user->response();

        $this->assertInstanceOf(JsonResponse::class, $response);
    }

    public function test_set_current_url(): void
    {
        $user = $this->createTraitUser();
        $result = $user->setCurrentUrl('/test');

        $this->assertSame($user, $result);

        $reflection = new \ReflectionProperty($user, 'currentUrl');
        $reflection->setAccessible(true);
        $this->assertNotEmpty($reflection->getValue($user));
    }

    public function test_current_url_initially_null(): void
    {
        $user = $this->createTraitUser();

        $reflection = new \ReflectionProperty($user, 'currentUrl');
        $reflection->setAccessible(true);
        $this->assertNull($reflection->getValue($user));
    }

    public function test_send_response_passes_through_non_json_response(): void
    {
        $user = $this->createTraitUser();

        $reflection = new \ReflectionMethod($user, 'sendResponse');
        $reflection->setAccessible(true);

        $result = $reflection->invoke($user, 'plain string');
        $this->assertSame('plain string', $result);
    }

    public function test_send_response_calls_send_on_json_response(): void
    {
        $user = $this->createTraitUser();
        $jsonResponse = new JsonResponse;

        $reflection = new \ReflectionMethod($user, 'sendResponse');
        $reflection->setAccessible(true);

        $result = $reflection->invoke($user, $jsonResponse);
        $this->assertInstanceOf(\Illuminate\Http\JsonResponse::class, $result);
    }
}
