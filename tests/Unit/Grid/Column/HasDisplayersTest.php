<?php

namespace Dcat\Admin\Tests\Unit\Grid\Column;

use Dcat\Admin\Grid\Column;
use Dcat\Admin\Grid\Column\HasDisplayers;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class HasDisplayersTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    public function test_trait_exists(): void
    {
        $this->assertTrue(trait_exists(HasDisplayers::class));
    }

    public function test_has_display_using_method(): void
    {
        $this->assertTrue(method_exists(Column::class, 'displayUsing'));
    }

    public function test_has_using_method(): void
    {
        $this->assertTrue(method_exists(Column::class, 'using'));
    }

    public function test_has_bold_method(): void
    {
        $this->assertTrue(method_exists(Column::class, 'bold'));
    }

    public function test_has_long2ip_method(): void
    {
        $this->assertTrue(method_exists(Column::class, 'long2ip'));
    }

    public function test_has_view_method(): void
    {
        $this->assertTrue(method_exists(Column::class, 'view'));
    }

    public function test_has_prepend_method(): void
    {
        $this->assertTrue(method_exists(Column::class, 'prepend'));
    }

    public function test_has_append_method(): void
    {
        $this->assertTrue(method_exists(Column::class, 'append'));
    }

    public function test_has_explode_method(): void
    {
        $this->assertTrue(method_exists(Column::class, 'explode'));
    }

    public function test_has_gravatar_method(): void
    {
        $this->assertTrue(method_exists(Column::class, 'gravatar'));
    }

    public function test_has_dot_method(): void
    {
        $this->assertTrue(method_exists(Column::class, 'dot'));
    }

    public function test_has_tree_method(): void
    {
        $this->assertTrue(method_exists(Column::class, 'tree'));
    }

    public function test_has_action_method(): void
    {
        $this->assertTrue(method_exists(Column::class, 'action'));
    }

    public function test_has_bool_method(): void
    {
        $this->assertTrue(method_exists(Column::class, 'bool'));
    }
}
