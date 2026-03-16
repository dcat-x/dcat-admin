<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Traits;

use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Traits\HasAuthorization;
use Symfony\Component\HttpKernel\Exception\HttpException;

class HasAuthorizationTest extends TestCase
{
    public function test_authorize_returns_true_by_default(): void
    {
        $obj = new class
        {
            use HasAuthorization;
        };

        $reflection = new \ReflectionMethod($obj, 'authorize');
        $result = $reflection->invoke($obj, null);

        $this->assertTrue($result);
    }

    public function test_authorize_can_be_overridden_to_return_false(): void
    {
        $obj = new class
        {
            use HasAuthorization;

            protected function authorize($user): bool
            {
                return $user !== null;
            }
        };

        $reflection = new \ReflectionMethod($obj, 'authorize');

        $this->assertFalse($reflection->invoke($obj, null));
    }

    public function test_authorize_override_with_user(): void
    {
        $obj = new class
        {
            use HasAuthorization;

            protected function authorize($user): bool
            {
                return $user !== null;
            }
        };

        $reflection = new \ReflectionMethod($obj, 'authorize');
        $mockUser = new \stdClass;

        $this->assertTrue($reflection->invoke($obj, $mockUser));
    }

    public function test_failed_authorization_aborts_with_403(): void
    {
        $obj = new class
        {
            use HasAuthorization;
        };

        $this->expectException(HttpException::class);

        $obj->failedAuthorization();
    }

    public function test_failed_authorization_status_code_is_403(): void
    {
        $obj = new class
        {
            use HasAuthorization;
        };

        try {
            $obj->failedAuthorization();
            $this->fail('Expected HttpException was not thrown.');
        } catch (HttpException $e) {
            $this->assertSame(403, $e->getStatusCode());
        }
    }

    public function test_method_signatures_are_expected(): void
    {
        $passesAuthorization = new \ReflectionMethod(HasAuthorization::class, 'passesAuthorization');
        $authorize = new \ReflectionMethod(HasAuthorization::class, 'authorize');
        $failedAuthorization = new \ReflectionMethod(HasAuthorization::class, 'failedAuthorization');

        $this->assertTrue($passesAuthorization->isPublic());
        $this->assertCount(0, $passesAuthorization->getParameters());

        $this->assertTrue($authorize->isProtected());
        $this->assertCount(1, $authorize->getParameters());

        $this->assertTrue($failedAuthorization->isPublic());
        $this->assertCount(0, $failedAuthorization->getParameters());
    }
}
