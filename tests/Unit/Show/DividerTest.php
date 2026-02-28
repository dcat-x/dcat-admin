<?php

namespace Dcat\Admin\Tests\Unit\Show;

use Dcat\Admin\Show\Divider;
use Dcat\Admin\Tests\TestCase;

class DividerTest extends TestCase
{
    public function test_divider_is_a_field_subclass(): void
    {
        $divider = new Divider;

        $this->assertInstanceOf(\Dcat\Admin\Show\Field::class, $divider);
    }

    public function test_render_returns_hr_markup(): void
    {
        $divider = new Divider;
        $output = $divider->render();

        $this->assertIsString($output);
        $this->assertStringContainsString('<hr', $output);
    }

    public function test_render_contains_col_sm_12_wrapper(): void
    {
        $divider = new Divider;
        $output = $divider->render();

        $this->assertStringContainsString('col-sm-12', $output);
    }

    public function test_render_contains_margin_style(): void
    {
        $divider = new Divider;
        $output = $divider->render();

        $this->assertStringContainsString('margin-top:15px', $output);
    }

    public function test_render_output_is_a_complete_div(): void
    {
        $divider = new Divider;
        $output = $divider->render();

        $expected = '<div class="col-sm-12"><hr style="margin-top:15px;"/></div>';
        $this->assertSame($expected, $output);
    }

    public function test_constructor_with_name_and_label(): void
    {
        $divider = new Divider('divider_name', 'Divider Label');

        // Even with constructor args, render still returns the fixed HR output
        $output = $divider->render();
        $this->assertStringContainsString('<hr', $output);
    }
}
