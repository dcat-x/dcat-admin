<?php

namespace Dcat\Admin\Tests\Unit\Support;

use Dcat\Admin\Support\Context;
use Dcat\Admin\Tests\TestCase;

class ContextTest extends TestCase
{
    protected Context $context;

    protected function setUp(): void
    {
        parent::setUp();
        $this->context = new Context;
    }

    public function test_set_single_key_value(): void
    {
        $result = $this->context->set('name', 'admin');
        $this->assertSame($this->context, $result);
        $this->assertSame('admin', $this->context->get('name'));
    }

    public function test_set_array_of_key_values(): void
    {
        $this->context->set(['a' => 1, 'b' => 2]);
        $this->assertSame(1, $this->context->get('a'));
        $this->assertSame(2, $this->context->get('b'));
    }

    public function test_set_numeric_key_uses_array_path_setter(): void
    {
        $this->context->set([0 => 'zero', 1 => 'one']);

        $this->assertSame('zero', $this->context->get('0'));
        $this->assertSame('one', $this->context->get('1'));
    }

    public function test_set_dot_notation_key(): void
    {
        $this->context->set('user.name', 'John');
        $this->assertSame('John', $this->context->get('user.name'));
    }

    public function test_get_returns_default_when_key_missing(): void
    {
        $this->assertNull($this->context->get('missing'));
        $this->assertSame('default', $this->context->get('missing', 'default'));
    }

    public function test_get_with_dot_notation(): void
    {
        $this->context->set('config.app.name', 'Test');
        $this->assertSame('Test', $this->context->get('config.app.name'));
    }

    public function test_remember_stores_and_returns_value(): void
    {
        $callCount = 0;
        $result = $this->context->remember('key', function () use (&$callCount) {
            $callCount++;

            return 'computed';
        });

        $this->assertSame('computed', $result);
        $this->assertSame(1, $callCount);
    }

    public function test_remember_returns_cached_value_without_recomputing(): void
    {
        $this->context->set('key', 'existing');
        $callCount = 0;

        $result = $this->context->remember('key', function () use (&$callCount) {
            $callCount++;

            return 'new';
        });

        $this->assertSame('existing', $result);
        $this->assertSame(0, $callCount);
    }

    public function test_get_array_returns_array(): void
    {
        $this->context->set('items', ['a', 'b', 'c']);
        $result = $this->context->getArray('items');
        $this->assertSame(['a', 'b', 'c'], $result);
    }

    public function test_get_array_returns_empty_array_for_missing_key(): void
    {
        $result = $this->context->getArray('missing');
        $this->assertSame([], $result);
    }

    public function test_add_appends_value_without_key(): void
    {
        $this->context->add('list', 'first');
        $this->context->add('list', 'second');
        $this->assertSame(['first', 'second'], $this->context->getArray('list'));
    }

    public function test_add_with_explicit_key(): void
    {
        $result = $this->context->add('map', 'value1', 'k1');
        $this->assertSame($this->context, $result);
        $this->context->add('map', 'value2', 'k2');
        $this->assertSame(['k1' => 'value1', 'k2' => 'value2'], $this->context->getArray('map'));
    }

    public function test_merge_combines_arrays(): void
    {
        $this->context->set('items', ['a', 'b']);
        $result = $this->context->merge('items', ['c', 'd']);
        $this->assertSame($this->context, $result);
        $this->assertSame(['a', 'b', 'c', 'd'], $this->context->getArray('items'));
    }

    public function test_merge_on_empty_key(): void
    {
        $this->context->merge('new_items', ['x', 'y']);
        $this->assertSame(['x', 'y'], $this->context->getArray('new_items'));
    }

    public function test_merge_with_empty_array_keeps_original_data(): void
    {
        $this->context->set('items', ['a', 'b']);
        $result = $this->context->merge('items', []);

        $this->assertSame($this->context, $result);
        $this->assertSame(['a', 'b'], $this->context->getArray('items'));
    }

    public function test_forget_removes_single_key(): void
    {
        $this->context->set('a', 1);
        $this->context->set('b', 2);
        $this->context->forget('a');
        $this->assertNull($this->context->get('a'));
        $this->assertSame(2, $this->context->get('b'));
    }

    public function test_forget_removes_multiple_keys(): void
    {
        $this->context->set(['a' => 1, 'b' => 2, 'c' => 3]);
        $this->context->forget(['a', 'c']);
        $this->assertNull($this->context->get('a'));
        $this->assertSame(2, $this->context->get('b'));
        $this->assertNull($this->context->get('c'));
    }

    public function test_flush_clears_all_data(): void
    {
        $this->context->set(['a' => 1, 'b' => 2, 'c' => 3]);
        $this->context->flush();
        $this->assertNull($this->context->get('a'));
        $this->assertNull($this->context->get('b'));
        $this->assertNull($this->context->get('c'));
    }

    public function test_fluent_property_access(): void
    {
        $this->context->set('favicon', '/icon.png');
        $this->assertSame('/icon.png', $this->context->favicon);
    }

    public function test_set_returns_self_for_chaining(): void
    {
        $result = $this->context->set('a', 1)->set('b', 2)->set('c', 3);
        $this->assertSame($this->context, $result);
        $this->assertSame(1, $this->context->get('a'));
        $this->assertSame(2, $this->context->get('b'));
        $this->assertSame(3, $this->context->get('c'));
    }

    public function test_overwrite_existing_value(): void
    {
        $this->context->set('key', 'old');
        $this->context->set('key', 'new');
        $this->assertSame('new', $this->context->get('key'));
    }
}
