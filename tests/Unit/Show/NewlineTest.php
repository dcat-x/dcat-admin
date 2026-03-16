<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Show;

use Dcat\Admin\Show\Newline;
use Dcat\Admin\Tests\TestCase;

class NewlineTest extends TestCase
{
    public function test_newline_is_a_field_subclass(): void
    {
        $newline = new Newline;

        $this->assertInstanceOf(\Dcat\Admin\Show\Field::class, $newline);
    }

    public function test_render_returns_empty_div(): void
    {
        $newline = new Newline;
        $output = $newline->render();

        $this->assertIsString($output);
        $this->assertStringContainsString('col-sm-12', $output);
    }

    public function test_render_does_not_contain_hr(): void
    {
        $newline = new Newline;
        $output = $newline->render();

        $this->assertStringNotContainsString('<hr', $output);
    }

    public function test_render_output_is_exact_empty_div(): void
    {
        $newline = new Newline;
        $output = $newline->render();

        $expected = '<div class="col-sm-12"></div>';
        $this->assertSame($expected, $output);
    }

    public function test_constructor_accepts_arguments(): void
    {
        $newline = new Newline('line', 'Line Label');

        // Render still returns the fixed empty div output
        $output = $newline->render();
        $this->assertSame('<div class="col-sm-12"></div>', $output);
    }

    public function test_render_is_different_from_divider(): void
    {
        $newline = new Newline;
        $divider = new \Dcat\Admin\Show\Divider;

        $this->assertNotSame($newline->render(), $divider->render());
    }
}
