<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Exception;

use Dcat\Admin\Exception\AdminException;
use Dcat\Admin\Exception\RuntimeException;
use Dcat\Admin\Tests\TestCase;
use Exception;

class RuntimeExceptionTest extends TestCase
{
    public function test_is_instance_of_admin_exception(): void
    {
        $e = new RuntimeException;

        $this->assertInstanceOf(AdminException::class, $e);
    }

    public function test_is_instance_of_exception(): void
    {
        $e = new RuntimeException;

        $this->assertInstanceOf(Exception::class, $e);
    }

    public function test_custom_message(): void
    {
        $e = new RuntimeException('runtime failure');

        $this->assertSame('runtime failure', $e->getMessage());
    }

    public function test_custom_code(): void
    {
        $e = new RuntimeException('error', 500);

        $this->assertSame(500, $e->getCode());
    }

    public function test_can_be_thrown_and_caught(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('runtime error');

        throw new RuntimeException('runtime error');
    }

    public function test_can_be_caught_as_admin_exception(): void
    {
        $caught = null;

        try {
            throw new RuntimeException('catch as admin');
        } catch (AdminException $e) {
            $caught = $e;
        }

        $this->assertInstanceOf(RuntimeException::class, $caught);
        $this->assertSame('catch as admin', $caught->getMessage());
    }
}
