<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Importers;

use Dcat\Admin\Grid\Importers\ImportResult;
use Dcat\Admin\Tests\TestCase;

class ImportResultTest extends TestCase
{
    public function test_defaults_to_zero(): void
    {
        $result = new ImportResult;

        $this->assertSame(0, $result->success);
        $this->assertSame(0, $result->failed);
        $this->assertSame([], $result->errors);
    }

    public function test_add_error(): void
    {
        $result = new ImportResult;
        $result->addError(1, 'name', 'Name is required');

        $this->assertSame([1 => ['name' => 'Name is required']], $result->errors);
    }

    public function test_add_multiple_errors_for_same_row(): void
    {
        $result = new ImportResult;
        $result->addError(1, 'name', 'Name is required');
        $result->addError(1, 'email', 'Email is invalid');

        $this->assertSame([
            1 => [
                'name' => 'Name is required',
                'email' => 'Email is invalid',
            ],
        ], $result->errors);
    }

    public function test_add_errors_for_different_rows(): void
    {
        $result = new ImportResult;
        $result->addError(1, 'name', 'Name is required');
        $result->addError(3, 'email', 'Email is invalid');

        $this->assertCount(2, $result->errors);
        $this->assertArrayHasKey(1, $result->errors);
        $this->assertArrayHasKey(3, $result->errors);
    }

    public function test_success_and_failed_counters(): void
    {
        $result = new ImportResult;
        $result->success = 5;
        $result->failed = 2;

        $this->assertSame(5, $result->success);
        $this->assertSame(2, $result->failed);
    }
}
