<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Widgets;

use Dcat\Admin\Grid\LazyRenderable;
use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Widgets\LazyTable;
use Dcat\Admin\Widgets\Widget;
use Mockery;

class LazyTableTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_lazy_table_extends_widget(): void
    {
        $table = new LazyTable;
        $this->assertInstanceOf(Widget::class, $table);
    }

    public function test_from_sets_renderable(): void
    {
        $renderable = Mockery::mock(LazyRenderable::class);
        $table = new LazyTable;
        $result = $table->from($renderable);

        $this->assertSame($table, $result);
        $this->assertSame($renderable, $table->getRenderable());
    }

    public function test_from_with_null_returns_self(): void
    {
        $table = new LazyTable;
        $result = $table->from(null);

        $this->assertSame($table, $result);
        $this->assertNull($table->getRenderable());
    }

    public function test_load_sets_value(): void
    {
        $table = new LazyTable;
        $result = $table->load(false);

        $this->assertSame($table, $result);

        $reflection = new \ReflectionProperty(LazyTable::class, 'load');
        $reflection->setAccessible(true);
        $this->assertFalse($reflection->getValue($table));
    }

    public function test_simple_sets_value(): void
    {
        $table = new LazyTable;
        $result = $table->simple();

        $this->assertSame($table, $result);

        $reflection = new \ReflectionProperty(LazyTable::class, 'simple');
        $reflection->setAccessible(true);
        $this->assertTrue($reflection->getValue($table));
    }

    public function test_simple_can_be_disabled(): void
    {
        $table = new LazyTable;
        $table->simple(false);

        $reflection = new \ReflectionProperty(LazyTable::class, 'simple');
        $reflection->setAccessible(true);
        $this->assertFalse($reflection->getValue($table));
    }

    public function test_on_load_appends_script(): void
    {
        $table = new LazyTable;
        $table->onLoad('console.log("loaded")');

        $reflection = new \ReflectionProperty(LazyTable::class, 'loadScript');
        $reflection->setAccessible(true);
        $this->assertStringContainsString('console.log("loaded")', $reflection->getValue($table));
        $this->assertStringContainsString('table:loaded', $reflection->getValue($table));
    }

    public function test_default_load_is_true(): void
    {
        $table = new LazyTable;

        $reflection = new \ReflectionProperty(LazyTable::class, 'load');
        $reflection->setAccessible(true);
        $this->assertTrue($reflection->getValue($table));
    }
}
