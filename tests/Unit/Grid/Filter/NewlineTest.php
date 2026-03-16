<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Filter;

use Dcat\Admin\Grid\Filter\AbstractFilter;
use Dcat\Admin\Grid\Filter\Newline;
use Dcat\Admin\Tests\TestCase;

class NewlineTest extends TestCase
{
    public function test_can_be_instantiated_without_arguments(): void
    {
        $newline = new Newline;

        $this->assertInstanceOf(Newline::class, $newline);
    }

    public function test_extends_abstract_filter(): void
    {
        $newline = new Newline;

        $this->assertInstanceOf(AbstractFilter::class, $newline);
    }

    public function test_condition_returns_null(): void
    {
        $newline = new Newline;

        $result = $newline->condition(['any' => 'value']);

        $this->assertNull($result);
    }

    public function test_render_returns_div_html(): void
    {
        $newline = new Newline;

        $result = $newline->render();

        $this->assertSame('<div class="col-md-12"></div>', $result);
    }

    public function test_render_returns_string(): void
    {
        $newline = new Newline;

        $result = $newline->render();

        $this->assertIsString($result);
    }

    public function test_condition_returns_null_with_empty_inputs(): void
    {
        $newline = new Newline;

        $result = $newline->condition([]);

        $this->assertNull($result);
    }
}
