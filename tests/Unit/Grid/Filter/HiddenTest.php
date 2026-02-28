<?php

namespace Dcat\Admin\Tests\Unit\Grid\Filter;

use Dcat\Admin\Grid\Filter\Hidden;
use Dcat\Admin\Tests\TestCase;

class HiddenTest extends TestCase
{
    public function test_constructor_sets_name_and_value(): void
    {
        $filter = new Hidden('status', 'active');
        $this->assertInstanceOf(Hidden::class, $filter);
    }

    public function test_condition_always_returns_null(): void
    {
        $filter = new Hidden('status', 'active');
        $result = $filter->condition(['status' => 'active']);

        $this->assertNull($result);
    }

    public function test_condition_with_empty_inputs(): void
    {
        $filter = new Hidden('type', '1');
        $result = $filter->condition([]);

        $this->assertNull($result);
    }

    public function test_render_returns_hidden_input(): void
    {
        $filter = new Hidden('status', 'active');
        $html = $filter->render();

        $this->assertStringContainsString('type=\'hidden\'', $html);
        $this->assertStringContainsString('name=\'status\'', $html);
        $this->assertStringContainsString('value=\'active\'', $html);
    }

    public function test_render_with_numeric_value(): void
    {
        $filter = new Hidden('page', '1');
        $html = $filter->render();

        $this->assertStringContainsString('name=\'page\'', $html);
        $this->assertStringContainsString('value=\'1\'', $html);
    }
}
