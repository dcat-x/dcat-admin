<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Filter\Presenter;

use Dcat\Admin\Grid\Filter\AbstractFilter;
use Dcat\Admin\Grid\Filter\Presenter\Checkbox;
use Dcat\Admin\Grid\Filter\Presenter\Radio;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use ReflectionProperty;

#[AllowMockObjectsWithoutExpectations]
class CheckboxTest extends TestCase
{
    protected function makeCheckbox(array $options = []): Checkbox
    {
        return new Checkbox($options);
    }

    protected function attachFilter(Checkbox $checkbox): void
    {
        $filter = $this->createMock(AbstractFilter::class);
        $filter->method('column')->willReturn('tags');

        $checkbox->setParent($filter);
    }

    public function test_extends_radio(): void
    {
        $checkbox = $this->makeCheckbox();

        $this->assertInstanceOf(Radio::class, $checkbox);
    }

    public function test_constructor_sets_options(): void
    {
        $options = ['php' => 'PHP', 'js' => 'JavaScript'];
        $checkbox = $this->makeCheckbox($options);

        $ref = new ReflectionProperty($checkbox, 'options');
        $ref->setAccessible(true);

        $this->assertSame($options, $ref->getValue($checkbox));
    }

    public function test_constructor_accepts_arrayable(): void
    {
        $collection = new Collection(['a' => 'Alpha', 'b' => 'Beta']);
        $checkbox = new Checkbox($collection);

        $ref = new ReflectionProperty($checkbox, 'options');
        $ref->setAccessible(true);

        $this->assertSame(['a' => 'Alpha', 'b' => 'Beta'], $ref->getValue($checkbox));
    }

    public function test_stacked_works_like_radio(): void
    {
        $checkbox = $this->makeCheckbox();

        $result = $checkbox->stacked();

        $this->assertSame($checkbox, $result);

        $ref = new ReflectionProperty($checkbox, 'inline');
        $ref->setAccessible(true);

        $this->assertFalse($ref->getValue($checkbox));
    }

    public function test_default_variables_returns_expected_structure(): void
    {
        $options = [1 => 'Read', 2 => 'Write', 3 => 'Execute'];
        $checkbox = $this->makeCheckbox($options);

        $vars = $checkbox->defaultVariables();

        $this->assertSame($options, $vars['options'] ?? null);
        $this->assertTrue($vars['inline']);
        $this->assertTrue($vars['showLabel']);
    }

    public function test_show_label_inherited_from_radio(): void
    {
        $checkbox = $this->makeCheckbox();

        $result = $checkbox->showLabel(false);

        $this->assertSame($checkbox, $result);

        $vars = $checkbox->defaultVariables();

        $this->assertFalse($vars['showLabel']);
    }

    public function test_view_returns_checkbox_view(): void
    {
        $checkbox = $this->makeCheckbox();

        $this->assertSame('admin::filter.checkbox', $checkbox->view());
    }
}
