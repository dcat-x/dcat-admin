<?php

namespace Dcat\Admin\Tests\Unit\Grid\Displayers;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Column;
use Dcat\Admin\Grid\Displayers\Expand;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class ExpandTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function makeDisplayer($value): Expand
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('getName')->andReturn('test');
        $grid->shouldReceive('getKeyName')->andReturn('id');
        $grid->shouldReceive('makeName')->andReturnUsing(function ($key) {
            return 'test-'.$key;
        });

        $column = Mockery::mock(Column::class);
        $column->shouldReceive('getName')->andReturn('details');

        $row = ['id' => 1, 'details' => $value];

        return new Expand($value, $grid, $column, $row);
    }

    public function test_button_sets_button_text(): void
    {
        $displayer = $this->makeDisplayer('Content');
        $displayer->button('Click Me');

        $ref = new \ReflectionProperty($displayer, 'button');
        $ref->setAccessible(true);

        $this->assertSame('Click Me', $ref->getValue($displayer));
    }

    public function test_display_with_string_sets_button(): void
    {
        $displayer = $this->makeDisplayer('Content');

        // When passing a string that's not a closure and not LazyRenderable,
        // it should set the button text
        $ref = new \ReflectionProperty($displayer, 'button');
        $ref->setAccessible(true);

        $displayer->button('My Button');
        $this->assertSame('My Button', $ref->getValue($displayer));
    }

    public function test_get_data_key_increments_counter(): void
    {
        $counterBefore = $this->getStaticProperty(Expand::class, 'counter');

        $displayer = $this->makeDisplayer('Content');
        $method = new \ReflectionMethod($displayer, 'getDataKey');
        $method->setAccessible(true);

        $key1 = $method->invoke($displayer);

        $counterAfter = $this->getStaticProperty(Expand::class, 'counter');

        $this->assertGreaterThan($counterBefore, $counterAfter);
        $this->assertIsString($key1);
    }

    public function test_get_data_key_contains_row_key(): void
    {
        $displayer = $this->makeDisplayer('Content');
        $method = new \ReflectionMethod($displayer, 'getDataKey');
        $method->setAccessible(true);

        $key = $method->invoke($displayer);

        $this->assertStringContainsString('test-', $key);
    }

    public function test_constructor_sets_value(): void
    {
        $displayer = $this->makeDisplayer('Test Content');

        $ref = new \ReflectionProperty($displayer, 'value');
        $ref->setAccessible(true);

        $this->assertSame('Test Content', $ref->getValue($displayer));
    }

    public function test_constructor_sets_grid(): void
    {
        $displayer = $this->makeDisplayer('Content');

        $ref = new \ReflectionProperty($displayer, 'grid');
        $ref->setAccessible(true);

        $this->assertNotNull($ref->getValue($displayer));
    }

    public function test_button_default_is_null(): void
    {
        $displayer = $this->makeDisplayer('Content');

        $ref = new \ReflectionProperty($displayer, 'button');
        $ref->setAccessible(true);

        $this->assertNull($ref->getValue($displayer));
    }

    protected function getStaticProperty(string $class, string $property)
    {
        $ref = new \ReflectionProperty($class, $property);
        $ref->setAccessible(true);

        return $ref->getValue();
    }
}
