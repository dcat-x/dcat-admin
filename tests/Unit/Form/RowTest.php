<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Form;

use Dcat\Admin\Form;
use Dcat\Admin\Form\Row;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Collection;
use Mockery;

class RowTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_implements_renderable(): void
    {
        $reflection = new \ReflectionClass(Row::class);

        $this->assertTrue($reflection->implementsInterface(Renderable::class));
    }

    public function test_constructor_calls_callback(): void
    {
        $called = false;
        $form = Mockery::mock(Form::class);

        new Row(function () use (&$called) {
            $called = true;
        }, $form);

        $this->assertTrue($called);
    }

    public function test_constructor_passes_row_to_callback(): void
    {
        $receivedRow = null;
        $form = Mockery::mock(Form::class);

        $row = new Row(function ($r) use (&$receivedRow) {
            $receivedRow = $r;
        }, $form);

        $this->assertSame($row, $receivedRow);
    }

    public function test_fields_returns_collection(): void
    {
        $form = Mockery::mock(Form::class);

        $row = new Row(function () {}, $form);

        $this->assertInstanceOf(Collection::class, $row->fields());
    }

    public function test_fields_initially_empty_after_noop_callback(): void
    {
        $form = Mockery::mock(Form::class);

        $row = new Row(function () {}, $form);

        $this->assertTrue($row->fields()->isEmpty());
    }

    public function test_default_width_sets_value(): void
    {
        $form = Mockery::mock(Form::class);
        $row = new Row(function () {}, $form);

        $row->defaultWidth(6);

        $reflection = new \ReflectionProperty(Row::class, 'defaultFieldWidth');
        $this->assertSame(6, $reflection->getValue($row));
    }

    public function test_default_width_returns_self(): void
    {
        $form = Mockery::mock(Form::class);
        $row = new Row(function () {}, $form);

        $result = $row->defaultWidth(6);

        $this->assertSame($row, $result);
    }

    public function test_width_sets_field_width(): void
    {
        $form = Mockery::mock(Form::class);
        $row = new Row(function () {}, $form);

        $row->width(4);

        $reflection = new \ReflectionProperty(Row::class, 'fieldWidth');
        $this->assertSame(4, $reflection->getValue($row));
    }

    public function test_width_returns_self(): void
    {
        $form = Mockery::mock(Form::class);
        $row = new Row(function () {}, $form);

        $result = $row->width(8);

        $this->assertSame($row, $result);
    }

    public function test_horizontal_returns_self(): void
    {
        $form = Mockery::mock(Form::class);
        $row = new Row(function () {}, $form);

        $result = $row->horizontal(true);

        $this->assertSame($row, $result);
    }

    public function test_horizontal_sets_value(): void
    {
        $form = Mockery::mock(Form::class);
        $row = new Row(function () {}, $form);

        $row->horizontal(true);

        $reflection = new \ReflectionProperty(Row::class, 'horizontal');
        $this->assertTrue($reflection->getValue($row));
    }

    public function test_set_fields_replaces_collection(): void
    {
        $form = Mockery::mock(Form::class);
        $row = new Row(function () {}, $form);

        $newCollection = collect([['width' => 6, 'element' => 'test']]);
        $result = $row->setFields($newCollection);

        $this->assertSame($row, $result);
        $this->assertSame($newCollection, $row->fields());
        $this->assertCount(1, $row->fields());
    }

    public function test_get_key_delegates_to_form(): void
    {
        $form = Mockery::mock(Form::class);
        $form->shouldReceive('getKey')->once()->andReturn(42);

        $row = new Row(function () {}, $form);

        $this->assertSame(42, $row->getKey());
    }

    public function test_model_delegates_to_form(): void
    {
        $form = Mockery::mock(Form::class);
        $model = new \Illuminate\Support\Fluent(['name' => 'test']);
        $form->shouldReceive('model')->once()->andReturn($model);

        $row = new Row(function () {}, $form);

        $this->assertSame($model, $row->model());
    }

    public function test_default_field_width_is_12(): void
    {
        $form = Mockery::mock(Form::class);
        $row = new Row(function () {}, $form);

        $reflection = new \ReflectionProperty(Row::class, 'defaultFieldWidth');
        $this->assertSame(12, $reflection->getValue($row));
    }

    public function test_default_horizontal_is_false(): void
    {
        $form = Mockery::mock(Form::class);
        $row = new Row(function () {}, $form);

        $reflection = new \ReflectionProperty(Row::class, 'horizontal');
        $this->assertFalse($reflection->getValue($row));
    }
}
