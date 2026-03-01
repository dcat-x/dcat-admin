<?php

namespace Dcat\Admin\Tests\Unit\Grid\Column\Filter;

use Dcat\Admin\Grid\Column\Filter;
use Dcat\Admin\Grid\Column\Filter\Checkbox;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class CheckboxTraitFilter extends Filter
{
    use Checkbox;

    protected $options = [];

    public function __construct(array $options = [])
    {
        $this->options = $options;
        $this->class = [
            'all' => 'test-all-class',
            'item' => 'test-item-class',
        ];
    }

    public function render()
    {
        return $this->renderCheckbox();
    }
}

class CheckboxTraitTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_render_checkbox_returns_null_when_display_false(): void
    {
        $filter = new CheckboxTraitFilter(['a' => 'A']);
        $filter->hide();

        $this->assertNull($filter->render());
    }

    public function test_trait_has_render_checkbox_method(): void
    {
        $this->assertTrue(method_exists(CheckboxTraitFilter::class, 'renderCheckbox'));
    }

    public function test_trait_has_add_script_method(): void
    {
        $this->assertTrue(method_exists(CheckboxTraitFilter::class, 'addScript'));
    }

    public function test_trait_has_render_options_method(): void
    {
        $this->assertTrue(method_exists(CheckboxTraitFilter::class, 'renderOptions'));
    }

    public function test_render_checkbox_method_exists(): void
    {
        $ref = new \ReflectionMethod(CheckboxTraitFilter::class, 'renderCheckbox');
        $this->assertFalse($ref->isPublic());
        $this->assertFalse($ref->isStatic());
    }

    public function test_add_script_method_exists(): void
    {
        $ref = new \ReflectionMethod(CheckboxTraitFilter::class, 'addScript');
        $this->assertFalse($ref->isPublic());
        $this->assertFalse($ref->isStatic());
    }
}
