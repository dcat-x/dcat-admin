<?php

namespace Dcat\Admin\Tests\Unit\Http\Actions;

use Dcat\Admin\Grid\RowAction;
use Dcat\Admin\Http\Actions\ImportButton;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class ImportButtonTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(ImportButton::class));
    }

    public function test_is_subclass_of_row_action(): void
    {
        $this->assertTrue(is_subclass_of(ImportButton::class, RowAction::class));
    }

    public function test_render_method_exists(): void
    {
        $this->assertTrue(method_exists(ImportButton::class, 'render'));
    }

    public function test_setup_script_method_exists(): void
    {
        $this->assertTrue(method_exists(ImportButton::class, 'setupScript'));
    }
}
