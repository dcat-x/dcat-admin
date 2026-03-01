<?php

namespace Dcat\Admin\Tests\Unit\Grid\Column\Filter;

use Dcat\Admin\Grid\Column\Filter;
use Dcat\Admin\Grid\Column\Filter\Input;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class InputTraitFilter extends Filter
{
    use Input;

    public function __construct()
    {
        $this->class = 'test-input-class';
    }

    public function render()
    {
        return $this->renderInput();
    }
}

class InputTraitTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_placeholder_sets_value(): void
    {
        $filter = new InputTraitFilter;
        $filter->placeholder('Enter value...');

        $ref = new \ReflectionProperty(InputTraitFilter::class, 'placeholder');
        $ref->setAccessible(true);
        $this->assertEquals('Enter value...', $ref->getValue($filter));
    }

    public function test_placeholder_returns_self(): void
    {
        $filter = new InputTraitFilter;
        $result = $filter->placeholder('test');

        $this->assertSame($filter, $result);
    }

    public function test_placeholder_default_null(): void
    {
        $filter = new InputTraitFilter;

        $ref = new \ReflectionProperty(InputTraitFilter::class, 'placeholder');
        $ref->setAccessible(true);
        $this->assertNull($ref->getValue($filter));
    }

    public function test_placeholder_accepts_null(): void
    {
        $filter = new InputTraitFilter;
        $filter->placeholder('something');
        $filter->placeholder(null);

        $ref = new \ReflectionProperty(InputTraitFilter::class, 'placeholder');
        $ref->setAccessible(true);
        $this->assertNull($ref->getValue($filter));
    }

    public function test_render_input_returns_null_when_display_false(): void
    {
        $filter = new InputTraitFilter;
        $filter->hide();

        $this->assertNull($filter->render());
    }

    public function test_trait_has_render_input_method(): void
    {
        $this->assertTrue(method_exists(InputTraitFilter::class, 'renderInput'));
    }

    public function test_trait_has_add_script_method(): void
    {
        $this->assertTrue(method_exists(InputTraitFilter::class, 'addScript'));
    }

    public function test_trait_has_value_filter_method(): void
    {
        $this->assertTrue(method_exists(InputTraitFilter::class, 'valueFilter'));
    }
}
