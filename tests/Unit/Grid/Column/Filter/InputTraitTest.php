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

    public function formAction()
    {
        return '/admin/filter';
    }

    public function getQueryName()
    {
        return 'filter_test_input';
    }
}

class InputTraitTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_placeholder_sets_value_and_returns_self(): void
    {
        $filter = new InputTraitFilter;
        $result = $filter->placeholder('Enter value...');

        $ref = new \ReflectionProperty(InputTraitFilter::class, 'placeholder');
        $ref->setAccessible(true);

        $this->assertSame($filter, $result);
        $this->assertEquals('Enter value...', $ref->getValue($filter));
    }

    public function test_placeholder_default_null_and_accepts_null(): void
    {
        $filter = new InputTraitFilter;

        $ref = new \ReflectionProperty(InputTraitFilter::class, 'placeholder');
        $ref->setAccessible(true);
        $this->assertNull($ref->getValue($filter));

        $filter->placeholder('something')->placeholder(null);
        $this->assertNull($ref->getValue($filter));
    }

    public function test_render_input_returns_null_when_display_false(): void
    {
        $filter = new InputTraitFilter;
        $filter->hide();

        $this->assertNull($filter->render());
    }

    public function test_render_input_contains_placeholder_and_input_css_class_when_visible(): void
    {
        $filter = new InputTraitFilter;
        $filter->placeholder('Search keyword');

        $html = $filter->render();

        $this->assertStringContainsString('Search keyword', $html);
        $this->assertStringContainsString('test-input-class', $html);
        $this->assertStringContainsString('icon-filter', $html);
    }

    public function test_value_filter_registers_resolving_callback_and_returns_self(): void
    {
        $filter = new InputTraitFilter;

        $result = $filter->valueFilter('name');

        $this->assertSame($filter, $result);

        $ref = new \ReflectionProperty(Filter::class, 'resolvings');
        $ref->setAccessible(true);
        $resolvings = $ref->getValue($filter);

        $this->assertIsArray($resolvings);
        $this->assertCount(1, $resolvings);
        $this->assertInstanceOf(\Closure::class, $resolvings[0]);
    }

    public function test_trait_method_signatures_are_expected(): void
    {
        $renderInput = new \ReflectionMethod(InputTraitFilter::class, 'renderInput');
        $addScript = new \ReflectionMethod(InputTraitFilter::class, 'addScript');
        $valueFilter = new \ReflectionMethod(InputTraitFilter::class, 'valueFilter');

        $this->assertTrue($renderInput->isProtected());
        $this->assertCount(0, $renderInput->getParameters());

        $this->assertTrue($addScript->isProtected());
        $this->assertCount(0, $addScript->getParameters());

        $this->assertTrue($valueFilter->isPublic());
        $this->assertCount(1, $valueFilter->getParameters());
        $this->assertSame('valueKey', $valueFilter->getParameters()[0]->getName());
    }
}
