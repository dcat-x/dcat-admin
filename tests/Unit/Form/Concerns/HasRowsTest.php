<?php

namespace Dcat\Admin\Tests\Unit\Form\Concerns;

use Dcat\Admin\Form\Concerns\HasRows;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class HasRowsTestHelper
{
    use HasRows;

    public function __call($method, $arguments)
    {
        return null;
    }
}

class HasRowsTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_rows_returns_empty_array_initially(): void
    {
        $helper = new HasRowsTestHelper;

        $this->assertEmpty($helper->rows());
    }

    public function test_rows_returns_array(): void
    {
        $helper = new HasRowsTestHelper;

        $this->assertIsArray($helper->rows());
    }

    public function test_row_returns_self(): void
    {
        $helper = new HasRowsTestHelper;

        $result = $helper->row(function () {});

        $this->assertSame($helper, $result);
    }

    public function test_rows_property_is_array(): void
    {
        $helper = new HasRowsTestHelper;

        $ref = new \ReflectionProperty($helper, 'rows');
        $ref->setAccessible(true);

        $this->assertIsArray($ref->getValue($helper));
    }

    public function test_row_adds_row_instance(): void
    {
        $helper = new HasRowsTestHelper;

        $helper->row(function () {});

        $this->assertCount(1, $helper->rows());
    }

    public function test_multiple_rows_added(): void
    {
        $helper = new HasRowsTestHelper;

        $helper->row(function () {});
        $helper->row(function () {});
        $helper->row(function () {});

        $this->assertCount(3, $helper->rows());
    }
}
