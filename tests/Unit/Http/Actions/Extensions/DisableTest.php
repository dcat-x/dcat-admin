<?php

namespace Dcat\Admin\Tests\Unit\Http\Actions\Extensions;

use Dcat\Admin\Grid\RowAction;
use Dcat\Admin\Http\Actions\Extensions\Disable;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class DisableTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(Disable::class));
    }

    public function test_is_subclass_of_row_action(): void
    {
        $this->assertTrue(is_subclass_of(Disable::class, RowAction::class));
    }

    public function test_title_method_exists(): void
    {
        $this->assertTrue(method_exists(Disable::class, 'title'));
    }

    public function test_handle_method_exists(): void
    {
        $this->assertTrue(method_exists(Disable::class, 'handle'));
    }
}
