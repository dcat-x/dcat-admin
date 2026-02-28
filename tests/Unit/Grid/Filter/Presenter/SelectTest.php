<?php

namespace Dcat\Admin\Tests\Unit\Grid\Filter\Presenter;

use Dcat\Admin\Grid\Filter\AbstractFilter;
use Dcat\Admin\Grid\Filter\Presenter\Select;
use Dcat\Admin\Tests\TestCase;
use ReflectionProperty;

class SelectTest extends TestCase
{
    protected function makeSelect($options = []): Select
    {
        return new Select($options);
    }

    protected function attachFilter(Select $select): AbstractFilter
    {
        $filter = $this->createMock(AbstractFilter::class);
        $filter->method('column')->willReturn('category_id');
        $filter->method('getValue')->willReturn(null);
        $filter->method('formatColumnClass')->willReturnCallback(fn ($col) => str_replace('.', '_', $col));

        $select->setParent($filter);

        return $filter;
    }

    public function test_constructor_sets_options(): void
    {
        $options = [1 => 'Cat A', 2 => 'Cat B'];
        $select = $this->makeSelect($options);

        $ref = new ReflectionProperty($select, 'options');
        $ref->setAccessible(true);

        $this->assertEquals($options, $ref->getValue($select));
    }

    public function test_config_with_key_value(): void
    {
        $select = $this->makeSelect();

        $result = $select->config('minimumInputLength', 3);

        $this->assertSame($select, $result);

        $ref = new ReflectionProperty($select, 'config');
        $ref->setAccessible(true);

        $this->assertEquals(['minimumInputLength' => 3], $ref->getValue($select));
    }

    public function test_config_with_array(): void
    {
        $select = $this->makeSelect();

        $select->config(['allowClear' => true, 'minimumInputLength' => 1]);

        $ref = new ReflectionProperty($select, 'config');
        $ref->setAccessible(true);
        $config = $ref->getValue($select);

        $this->assertTrue($config['allowClear']);
        $this->assertEquals(1, $config['minimumInputLength']);
    }

    public function test_config_merges_values(): void
    {
        $select = $this->makeSelect();

        $select->config('key1', 'val1');
        $select->config('key2', 'val2');

        $ref = new ReflectionProperty($select, 'config');
        $ref->setAccessible(true);
        $config = $ref->getValue($select);

        $this->assertEquals('val1', $config['key1']);
        $this->assertEquals('val2', $config['key2']);
    }

    public function test_disable_select_all(): void
    {
        $select = $this->makeSelect();

        $result = $select->disableSelectAll();

        $this->assertSame($select, $result);

        $ref = new ReflectionProperty($select, 'selectAll');
        $ref->setAccessible(true);

        $this->assertFalse($ref->getValue($select));
    }

    public function test_select_all_enabled_by_default(): void
    {
        $select = $this->makeSelect();

        $ref = new ReflectionProperty($select, 'selectAll');
        $ref->setAccessible(true);

        $this->assertTrue($ref->getValue($select));
    }

    public function test_placeholder_sets_and_gets(): void
    {
        $select = $this->makeSelect();

        $result = $select->placeholder('Pick one...');

        $this->assertSame($select, $result);

        $ref = new ReflectionProperty($select, 'placeholder');
        $ref->setAccessible(true);

        $this->assertEquals('Pick one...', $ref->getValue($select));
    }

    public function test_placeholder_returns_default_when_not_set(): void
    {
        $select = $this->makeSelect();

        $placeholder = $select->placeholder();

        $this->assertNotNull($placeholder);
        $this->assertIsString($placeholder);
    }

    public function test_get_element_class_from_column(): void
    {
        $select = $this->makeSelect();
        $this->attachFilter($select);

        $class = $select->getElementClass();

        $this->assertEquals('category_id', $class);
    }

    public function test_get_element_class_replaces_dots(): void
    {
        $select = $this->makeSelect();

        $filter = $this->createMock(AbstractFilter::class);
        $filter->method('column')->willReturn('user.name');
        $filter->method('getValue')->willReturn(null);

        $select->setParent($filter);

        $class = $select->getElementClass();

        $this->assertEquals('user_name', $class);
    }

    public function test_get_element_class_selector(): void
    {
        $select = $this->makeSelect();
        $this->attachFilter($select);

        $selector = $select->getElementClassSelector();

        $this->assertEquals('.category_id', $selector);
    }

    public function test_add_default_config_does_not_override_existing(): void
    {
        $select = $this->makeSelect();

        $select->config('allowClear', false);
        $select->addDefaultConfig('allowClear', true);

        $ref = new ReflectionProperty($select, 'config');
        $ref->setAccessible(true);
        $config = $ref->getValue($select);

        $this->assertFalse($config['allowClear']);
    }

    public function test_add_default_config_sets_when_missing(): void
    {
        $select = $this->makeSelect();

        $select->addDefaultConfig('allowClear', true);

        $ref = new ReflectionProperty($select, 'config');
        $ref->setAccessible(true);
        $config = $ref->getValue($select);

        $this->assertTrue($config['allowClear']);
    }

    public function test_view_returns_select_view(): void
    {
        $select = $this->makeSelect();

        $this->assertEquals('admin::filter.select', $select->view());
    }
}
