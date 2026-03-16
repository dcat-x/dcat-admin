<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Exception;

use Dcat\Admin\Exception\AdminException;
use Dcat\Admin\Tests\TestCase;
use Exception;

class AdminExceptionTest extends TestCase
{
    public function test_is_instance_of_exception(): void
    {
        $e = new AdminException;

        $this->assertInstanceOf(Exception::class, $e);
    }

    public function test_default_message_and_code(): void
    {
        $e = new AdminException;

        $this->assertSame('', $e->getMessage());
        $this->assertSame(0, $e->getCode());
    }

    public function test_custom_message(): void
    {
        $e = new AdminException('something went wrong');

        $this->assertSame('something went wrong', $e->getMessage());
    }

    public function test_custom_code(): void
    {
        $e = new AdminException('error', 500);

        $this->assertSame(500, $e->getCode());
    }

    public function test_can_be_thrown_and_caught(): void
    {
        $this->expectException(AdminException::class);
        $this->expectExceptionMessage('test message');
        $this->expectExceptionCode(42);

        throw new AdminException('test message', 42);
    }

    public function test_can_be_caught_as_exception(): void
    {
        $caught = null;

        try {
            throw new AdminException('catch me');
        } catch (Exception $e) {
            $caught = $e;
        }

        $this->assertInstanceOf(AdminException::class, $caught);
        $this->assertSame('catch me', $caught->getMessage());
    }
}
