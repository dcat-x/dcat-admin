<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Grid\Filter\Presenter;

use Dcat\Admin\Grid\Filter\AbstractFilter;
use Dcat\Admin\Grid\Filter\Presenter\MultipleSelect;
use Dcat\Admin\Grid\Filter\Presenter\Select;
use Dcat\Admin\Tests\TestCase;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use ReflectionProperty;

#[AllowMockObjectsWithoutExpectations]
class MultipleSelectTest extends TestCase
{
    protected function makeMultipleSelect($options = []): MultipleSelect
    {
        return new MultipleSelect($options);
    }

    protected function attachFilter(MultipleSelect $select): AbstractFilter
    {
        $filter = $this->createMock(AbstractFilter::class);
        $filter->method('column')->willReturn('tag_ids');
        $filter->method('getValue')->willReturn(null);
        $filter->method('formatColumnClass')->willReturnCallback(fn ($col) => str_replace('.', '_', $col));

        $select->setParent($filter);

        return $filter;
    }

    public function test_extends_select(): void
    {
        $ms = $this->makeMultipleSelect();

        $this->assertInstanceOf(Select::class, $ms);
    }

    public function test_constructor_sets_options(): void
    {
        $options = [1 => 'Tag A', 2 => 'Tag B', 3 => 'Tag C'];
        $ms = $this->makeMultipleSelect($options);

        $ref = new ReflectionProperty($ms, 'options');
        $ref->setAccessible(true);

        $this->assertSame($options, $ref->getValue($ms));
    }

    public function test_inherits_config_from_select(): void
    {
        $ms = $this->makeMultipleSelect();

        $result = $ms->config('minimumInputLength', 2);

        $this->assertSame($ms, $result);

        $ref = new ReflectionProperty($ms, 'config');
        $ref->setAccessible(true);

        $this->assertSame(['minimumInputLength' => 2], $ref->getValue($ms));
    }

    public function test_inherits_disable_select_all(): void
    {
        $ms = $this->makeMultipleSelect();

        $result = $ms->disableSelectAll();

        $this->assertSame($ms, $result);

        $ref = new ReflectionProperty($ms, 'selectAll');
        $ref->setAccessible(true);

        $this->assertFalse($ref->getValue($ms));
    }

    public function test_inherits_placeholder_from_select(): void
    {
        $ms = $this->makeMultipleSelect();

        $result = $ms->placeholder('Select tags...');

        $this->assertSame($ms, $result);

        $ref = new ReflectionProperty($ms, 'placeholder');
        $ref->setAccessible(true);

        $this->assertSame('Select tags...', $ref->getValue($ms));
    }

    public function test_get_element_class(): void
    {
        $ms = $this->makeMultipleSelect();
        $this->attachFilter($ms);

        $class = $ms->getElementClass();

        $this->assertSame('tag_ids', $class);
    }

    public function test_get_element_class_selector(): void
    {
        $ms = $this->makeMultipleSelect();
        $this->attachFilter($ms);

        $selector = $ms->getElementClassSelector();

        $this->assertSame('.tag_ids', $selector);
    }

    public function test_view_returns_multipleselect_view(): void
    {
        $ms = $this->makeMultipleSelect();

        $this->assertSame('admin::filter.multipleselect', $ms->view());
    }
}
