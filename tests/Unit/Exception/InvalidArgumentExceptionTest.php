<?php

namespace Dcat\Admin\Tests\Unit\Exception;

use Dcat\Admin\Exception\AdminException;
use Dcat\Admin\Exception\InvalidArgumentException;
use Dcat\Admin\Tests\TestCase;
use Exception;

class InvalidArgumentExceptionTest extends TestCase
{
    public function test_is_instance_of_admin_exception(): void
    {
        $e = new InvalidArgumentException;

        $this->assertInstanceOf(AdminException::class, $e);
    }

    public function test_is_instance_of_exception(): void
    {
        $e = new InvalidArgumentException;

        $this->assertInstanceOf(Exception::class, $e);
    }

    public function test_custom_message(): void
    {
        $e = new InvalidArgumentException('invalid argument provided');

        $this->assertSame('invalid argument provided', $e->getMessage());
    }

    public function test_custom_code(): void
    {
        $e = new InvalidArgumentException('error', 422);

        $this->assertSame(422, $e->getCode());
    }

    public function test_can_be_thrown_and_caught(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('bad argument');

        throw new InvalidArgumentException('bad argument');
    }

    public function test_can_be_caught_as_admin_exception(): void
    {
        $caught = null;

        try {
            throw new InvalidArgumentException('catch as admin');
        } catch (AdminException $e) {
            $caught = $e;
        }

        $this->assertInstanceOf(InvalidArgumentException::class, $caught);
        $this->assertSame('catch as admin', $caught->getMessage());
    }
}
