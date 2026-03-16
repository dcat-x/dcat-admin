<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Widgets;

use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Widgets\Checkbox;

class CheckboxTest extends TestCase
{
    public function test_constructor(): void
    {
        $checkbox = new Checkbox('colors', [1 => 'Red', 2 => 'Blue']);
        $this->assertInstanceOf(Checkbox::class, $checkbox);
    }

    public function test_check_with_array(): void
    {
        $checkbox = new Checkbox;
        $result = $checkbox->check([1, 3]);
        $this->assertSame($checkbox, $result);
        $vars = $checkbox->defaultVariables();
        $this->assertSame([1, 3], $vars['checked']);
    }

    public function test_check_with_single_value(): void
    {
        $checkbox = new Checkbox;
        $checkbox->check(2);
        $vars = $checkbox->defaultVariables();
        $this->assertIsArray($vars['checked']);
        $this->assertContains(2, $vars['checked']);
    }

    public function test_default_checked_empty(): void
    {
        $checkbox = new Checkbox;
        $vars = $checkbox->defaultVariables();
        $this->assertIsArray($vars['checked']);
        $this->assertEmpty($vars['checked']);
    }

    public function test_check_all(): void
    {
        $checkbox = new Checkbox;
        $checkbox->options([1 => 'A', 2 => 'B', 3 => 'C']);
        $checkbox->checkAll();
        $vars = $checkbox->defaultVariables();
        $this->assertSame([1, 2, 3], $vars['checked']);
    }

    public function test_check_all_with_excepts(): void
    {
        $checkbox = new Checkbox;
        $checkbox->options([1 => 'A', 2 => 'B', 3 => 'C']);
        $checkbox->checkAll([2]);
        $vars = $checkbox->defaultVariables();
        $this->assertContains(1, $vars['checked']);
        $this->assertNotContains(2, $vars['checked']);
        $this->assertContains(3, $vars['checked']);
    }

    public function test_inline(): void
    {
        $checkbox = new Checkbox;
        $checkbox->inline();
        $vars = $checkbox->defaultVariables();
        $this->assertTrue($vars['inline']);
    }

    public function test_options_set(): void
    {
        $checkbox = new Checkbox;
        $checkbox->options([1 => 'X', 2 => 'Y']);
        $vars = $checkbox->defaultVariables();
        $this->assertSame([1 => 'X', 2 => 'Y'], $vars['options']);
    }

    public function test_make_factory(): void
    {
        $checkbox = Checkbox::make('test', [1 => 'A']);
        $this->assertInstanceOf(Checkbox::class, $checkbox);
    }
}
