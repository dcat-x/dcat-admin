<?php

namespace Dcat\Admin\Tests\Unit\Form;

use Dcat\Admin\Form;
use Dcat\Admin\Form\Layout;
use Dcat\Admin\Layout\Column;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class LayoutTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function createLayout(): Layout
    {
        $form = Mockery::mock(Form::class);

        return new Layout($form);
    }

    public function test_constructor_creates_instance(): void
    {
        $layout = $this->createLayout();
        $this->assertInstanceOf(Layout::class, $layout);
    }

    public function test_has_columns_false_by_default(): void
    {
        $layout = $this->createLayout();
        $this->assertFalse($layout->hasColumns());
    }

    public function test_has_blocks_false_by_default(): void
    {
        $layout = $this->createLayout();
        $this->assertFalse($layout->hasBlocks());
    }

    public function test_get_columns_empty_by_default(): void
    {
        $layout = $this->createLayout();
        $this->assertIsArray($layout->getColumns());
        $this->assertEmpty($layout->getColumns());
    }

    public function test_column_adds_and_returns_column(): void
    {
        $layout = $this->createLayout();
        $column = $layout->column(6, 'content');
        $this->assertInstanceOf(Column::class, $column);
        $this->assertCount(1, $layout->getColumns());
    }

    public function test_multiple_columns(): void
    {
        $layout = $this->createLayout();
        $layout->column(6, 'left');
        $layout->column(6, 'right');
        $this->assertCount(2, $layout->getColumns());
    }

    public function test_prepend_adds_column_at_beginning(): void
    {
        $layout = $this->createLayout();
        $layout->column(6, 'second');
        $layout->prepend(6, 'first');
        $columns = $layout->getColumns();
        $this->assertCount(2, $columns);
        // first item should be the prepended one
        $this->assertInstanceOf(Column::class, $columns[0]);
    }

    public function test_set_columns(): void
    {
        $layout = $this->createLayout();
        $layout->column(6, 'original');

        $newColumns = [new Column('new', 12)];
        $result = $layout->setColumns($newColumns);

        $this->assertSame($layout, $result);
        $this->assertCount(1, $layout->getColumns());
    }

    public function test_reset_clears_columns(): void
    {
        $layout = $this->createLayout();
        $layout->column(6, 'content');
        $layout->reset();

        $this->assertEmpty($layout->getColumns());
        $this->assertFalse($layout->hasColumns());
    }

    public function test_add_field_stores_field(): void
    {
        $layout = $this->createLayout();
        $field = Mockery::mock(\Dcat\Admin\Form\Field::class);

        $layout->addField($field);

        // Verify via reflection that currentFields has the field
        $ref = new \ReflectionProperty($layout, 'currentFields');
        $ref->setAccessible(true);
        $fields = $ref->getValue($layout);
        $this->assertCount(1, $fields);
        $this->assertSame($field, $fields[0]);
    }

    public function test_build_returns_html_with_row_div(): void
    {
        $layout = $this->createLayout();
        $html = $layout->build();
        $this->assertStringStartsWith('<div class="row">', $html);
        $this->assertStringEndsWith('</div>', $html);
    }

    public function test_build_with_additional_content(): void
    {
        $layout = $this->createLayout();
        $html = $layout->build('<script>alert(1)</script>');
        $this->assertStringContainsString('</div><script>alert(1)</script>', $html);
    }
}
