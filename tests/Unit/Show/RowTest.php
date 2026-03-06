<?php

namespace Dcat\Admin\Tests\Unit\Show;

use Dcat\Admin\Show;
use Dcat\Admin\Show\Field;
use Dcat\Admin\Show\Row;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Support\Collection;

class RowTest extends TestCase
{
    protected function makeShow(array $data = []): Show
    {
        return new Show(array_merge(['name' => 'Test', 'email' => 'test@example.com', 'age' => 30], $data));
    }

    public function test_constructor_initializes_fields_collection(): void
    {
        $show = $this->makeShow();

        $row = new Row(function ($row) {
            // empty callback
        }, $show);

        $this->assertInstanceOf(Collection::class, $row->fields());
    }

    public function test_constructor_executes_callback(): void
    {
        $show = $this->makeShow();
        $callbackExecuted = false;

        $row = new Row(function ($r) use (&$callbackExecuted) {
            $callbackExecuted = true;
        }, $show);

        $this->assertTrue($callbackExecuted);
    }

    public function test_constructor_passes_row_to_callback(): void
    {
        $show = $this->makeShow();
        $receivedRow = null;

        $row = new Row(function ($r) use (&$receivedRow) {
            $receivedRow = $r;
        }, $show);

        $this->assertSame($row, $receivedRow);
    }

    public function test_field_method_adds_field_to_collection(): void
    {
        $show = $this->makeShow();

        $row = new Row(function ($row) {
            $row->field('name', 'Name');
        }, $show);

        $fields = $row->fields();
        $this->assertCount(1, $fields);
    }

    public function test_field_method_returns_field_instance(): void
    {
        $show = $this->makeShow();
        $returnedField = null;

        $row = new Row(function ($row) use (&$returnedField) {
            $returnedField = $row->field('name', 'Name');
        }, $show);

        $this->assertInstanceOf(Field::class, $returnedField);
    }

    public function test_field_stores_width_and_element(): void
    {
        $show = $this->makeShow();

        $row = new Row(function ($row) {
            $row->field('name', 'Name');
        }, $show);

        $fieldEntry = $row->fields()->first();
        $this->assertIsInt($fieldEntry['width'] ?? null);
        $this->assertInstanceOf(Field::class, $fieldEntry['element'] ?? null);
    }

    public function test_default_field_width_is_twelve(): void
    {
        $show = $this->makeShow();

        $row = new Row(function ($row) {
            $row->field('name', 'Name');
        }, $show);

        $fieldEntry = $row->fields()->first();
        $this->assertSame(12, $fieldEntry['width']);
    }

    public function test_width_changes_default_for_subsequent_fields(): void
    {
        $show = $this->makeShow();

        $row = new Row(function ($row) {
            $row->width(6);
            $row->field('name', 'Name');
            $row->field('email', 'Email');
        }, $show);

        $fields = $row->fields();
        $this->assertSame(6, $fields[0]['width']);
        $this->assertSame(6, $fields[1]['width']);
    }

    public function test_width_returns_self_for_chaining(): void
    {
        $show = $this->makeShow();
        $widthResult = null;

        $row = new Row(function ($row) use (&$widthResult) {
            $widthResult = $row->width(4);
        }, $show);

        $this->assertSame($row, $widthResult);
    }

    public function test_multiple_fields_added_via_callback(): void
    {
        $show = $this->makeShow();

        $row = new Row(function ($row) {
            $row->field('name', 'Name');
            $row->field('email', 'Email');
            $row->field('age', 'Age');
        }, $show);

        $this->assertCount(3, $row->fields());
    }

    public function test_magic_get_adds_field(): void
    {
        $show = $this->makeShow();

        $row = new Row(function ($row) {
            $row->name;
        }, $show);

        $fields = $row->fields();
        $this->assertCount(1, $fields);
        $this->assertInstanceOf(Field::class, $fields->first()['element']);
    }

    public function test_fields_returns_collection(): void
    {
        $show = $this->makeShow();

        $row = new Row(function ($row) {
            // empty
        }, $show);

        $this->assertInstanceOf(Collection::class, $row->fields());
        $this->assertCount(0, $row->fields());
    }

    public function test_mixed_width_fields(): void
    {
        $show = $this->makeShow();

        $row = new Row(function ($row) {
            $row->width(4);
            $row->field('name', 'Name');
            $row->width(8);
            $row->field('email', 'Email');
        }, $show);

        $fields = $row->fields();
        $this->assertSame(4, $fields[0]['width']);
        $this->assertSame(8, $fields[1]['width']);
    }
}
