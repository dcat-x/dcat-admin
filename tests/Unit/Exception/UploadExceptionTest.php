<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Exception;

use Dcat\Admin\Exception\AdminException;
use Dcat\Admin\Exception\UploadException;
use Dcat\Admin\Tests\TestCase;
use Exception;

class UploadExceptionTest extends TestCase
{
    public function test_is_instance_of_admin_exception(): void
    {
        $e = new UploadException;

        $this->assertInstanceOf(AdminException::class, $e);
    }

    public function test_is_instance_of_exception(): void
    {
        $e = new UploadException;

        $this->assertInstanceOf(Exception::class, $e);
    }

    public function test_custom_message(): void
    {
        $e = new UploadException('upload failed');

        $this->assertSame('upload failed', $e->getMessage());
    }

    public function test_custom_code(): void
    {
        $e = new UploadException('error', 413);

        $this->assertSame(413, $e->getCode());
    }

    public function test_can_be_thrown_and_caught(): void
    {
        $this->expectException(UploadException::class);
        $this->expectExceptionMessage('file too large');

        throw new UploadException('file too large');
    }

    public function test_can_be_caught_as_admin_exception(): void
    {
        $caught = null;

        try {
            throw new UploadException('upload error');
        } catch (AdminException $e) {
            $caught = $e;
        }

        $this->assertInstanceOf(UploadException::class, $caught);
        $this->assertSame('upload error', $caught->getMessage());
    }
}
