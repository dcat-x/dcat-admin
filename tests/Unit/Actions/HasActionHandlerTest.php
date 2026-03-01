<?php

namespace Dcat\Admin\Tests\Unit\Actions;

use Dcat\Admin\Actions\HasActionHandler;
use Dcat\Admin\Actions\Response;
use Dcat\Admin\Tests\TestCase;

class HasActionHandlerTest extends TestCase
{
    protected function createTraitUser(): object
    {
        return new class
        {
            use HasActionHandler;

            protected $method = 'POST';

            public function selector()
            {
                return '.test-action';
            }

            public function getKey()
            {
                return 1;
            }
        };
    }

    public function test_response_returns_response_instance(): void
    {
        $user = $this->createTraitUser();
        $response = $user->response();

        $this->assertInstanceOf(Response::class, $response);
    }

    public function test_response_returns_same_instance(): void
    {
        $user = $this->createTraitUser();
        $response1 = $user->response();
        $response2 = $user->response();

        $this->assertSame($response1, $response2);
    }

    public function test_method_returns_value(): void
    {
        $user = $this->createTraitUser();
        $this->assertEquals('POST', $user->method());
    }

    public function test_make_called_class(): void
    {
        $user = $this->createTraitUser();
        $result = $user->makeCalledClass();

        $this->assertIsString($result);
        $this->assertStringNotContainsString('\\', $result);
    }

    public function test_confirm_returns_void(): void
    {
        $user = $this->createTraitUser();
        $this->assertNull($user->confirm());
    }
}
