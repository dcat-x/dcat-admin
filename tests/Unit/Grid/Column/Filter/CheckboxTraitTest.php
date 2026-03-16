<?php

declare(strict_types=1);

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

    public function formAction()
    {
        return '/admin/filter';
    }

    public function getQueryName()
    {
        return 'filter_test_checkbox';
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

    public function test_render_checkbox_contains_expected_markup_when_visible(): void
    {
        $filter = new CheckboxTraitFilter(['a' => 'A', 'b' => 'B']);
        request()->merge(['filter_test_checkbox' => ['a']]);

        $html = $filter->render();

        $this->assertStringContainsString('test-all-class', $html);
        $this->assertStringContainsString('test-item-class', $html);
        $this->assertStringContainsString('name="filter_test_checkbox[]"', $html);
        $this->assertStringContainsString('>A<', $html);
        $this->assertStringContainsString('>B<', $html);
    }

    public function test_render_options_marks_selected_option_as_checked(): void
    {
        $filter = new CheckboxTraitFilter(['a' => 'A', 'b' => 'B']);

        $ref = new \ReflectionMethod(CheckboxTraitFilter::class, 'renderOptions');
        $ref->setAccessible(true);
        $html = $ref->invoke($filter, ['b']);

        $this->assertStringContainsString('value="b"', $html);
        $this->assertStringContainsString('checked', $html);
    }

    public function test_protected_method_signatures_are_expected(): void
    {
        $renderCheckbox = new \ReflectionMethod(CheckboxTraitFilter::class, 'renderCheckbox');
        $addScript = new \ReflectionMethod(CheckboxTraitFilter::class, 'addScript');
        $renderOptions = new \ReflectionMethod(CheckboxTraitFilter::class, 'renderOptions');

        $this->assertTrue($renderCheckbox->isProtected());
        $this->assertCount(0, $renderCheckbox->getParameters());

        $this->assertTrue($addScript->isProtected());
        $this->assertCount(0, $addScript->getParameters());

        $this->assertTrue($renderOptions->isProtected());
        $this->assertCount(1, $renderOptions->getParameters());
    }
}
