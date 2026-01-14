<?php

namespace Dcat\Admin\Tests\Unit\Grid;

use Dcat\Admin\Grid\Column;
use Dcat\Admin\Tests\TestCase;

class ColumnTest extends TestCase
{
    public function test_column_name(): void
    {
        $column = new Column('name', 'Name');
        $this->assertEquals('name', $column->getName());
    }

    public function test_column_label(): void
    {
        $column = new Column('name', 'Name Label');
        $this->assertEquals('Name Label', $column->getLabel());
    }

    public function test_column_label_auto_format(): void
    {
        $column = new Column('user_name', '');
        // Column 会自动将下划线转换为空格，首字母大写
        $this->assertStringContainsString('user', strtolower($column->getLabel()));
    }

    public function test_column_width(): void
    {
        $column = new Column('name', 'Name');
        $result = $column->width('100px');

        // width 返回 $this
        $this->assertInstanceOf(Column::class, $result);
    }

    public function test_column_attributes(): void
    {
        $column = new Column('name', 'Name');
        $column->setAttributes(['class' => 'custom-class', 'data-id' => '123']);

        $attributes = $column->getAttributes();
        $this->assertEquals('custom-class', $attributes['class']);
        $this->assertEquals('123', $attributes['data-id']);
    }

    public function test_column_style(): void
    {
        $column = new Column('name', 'Name');
        $result = $column->style('color: red;');

        // style 返回 $this
        $this->assertInstanceOf(Column::class, $result);
        $attributes = $column->getAttributes();
        $this->assertEquals('color: red;', $attributes['style']);
    }

    public function test_column_extensions(): void
    {
        $extensions = Column::extensions();
        $this->assertIsArray($extensions);
        $this->assertArrayHasKey('switch', $extensions);
        $this->assertArrayHasKey('image', $extensions);
        $this->assertArrayHasKey('label', $extensions);
        $this->assertArrayHasKey('badge', $extensions);
        $this->assertArrayHasKey('link', $extensions);
    }

    public function test_column_extend(): void
    {
        Column::extend('custom_displayer', \stdClass::class);
        $extensions = Column::extensions();
        $this->assertArrayHasKey('custom_displayer', $extensions);
    }

    public function test_column_set_original(): void
    {
        $column = new Column('name', 'Name');
        $column->setOriginal('original_value');
        $this->assertEquals('original_value', $column->getOriginal());
    }

    public function test_column_constants(): void
    {
        $this->assertEquals('__row_selector__', Column::SELECT_COLUMN_NAME);
        $this->assertEquals('__actions__', Column::ACTION_COLUMN_NAME);
    }
}
