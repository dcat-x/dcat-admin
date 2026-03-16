<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Traits;

use Dcat\Admin\Admin;
use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Traits\HasBuilderEvents;

class BuilderEventTestClass
{
    use HasBuilderEvents;

    public function triggerResolving(): void
    {
        $this->callResolving();
    }

    public function triggerComposing(): void
    {
        $this->callComposing();
    }
}

class HasBuilderEventsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Flush context to ensure clean state
        Admin::context()->flush();
    }

    public function test_resolving_registers_listener(): void
    {
        $called = false;

        BuilderEventTestClass::resolving(function () use (&$called) {
            $called = true;
        });

        $obj = new BuilderEventTestClass;
        $obj->triggerResolving();

        $this->assertTrue($called);
    }

    public function test_composing_registers_listener(): void
    {
        $called = false;

        BuilderEventTestClass::composing(function () use (&$called) {
            $called = true;
        });

        $obj = new BuilderEventTestClass;
        $obj->triggerComposing();

        $this->assertTrue($called);
    }

    public function test_listener_receives_instance(): void
    {
        $receivedInstance = null;

        BuilderEventTestClass::resolving(function ($instance) use (&$receivedInstance) {
            $receivedInstance = $instance;
        });

        $obj = new BuilderEventTestClass;
        $obj->triggerResolving();

        $this->assertSame($obj, $receivedInstance);
    }

    public function test_once_listener_fires_only_once(): void
    {
        $count = 0;

        BuilderEventTestClass::resolving(function () use (&$count) {
            $count++;
        }, true);

        $obj = new BuilderEventTestClass;
        $obj->triggerResolving();
        $obj->triggerResolving();

        $this->assertSame(1, $count);
    }

    public function test_non_once_listener_fires_multiple_times(): void
    {
        $count = 0;

        BuilderEventTestClass::resolving(function () use (&$count) {
            $count++;
        });

        $obj = new BuilderEventTestClass;
        $obj->triggerResolving();
        $obj->triggerResolving();

        $this->assertSame(2, $count);
    }

    public function test_multiple_listeners_fire_in_order(): void
    {
        $order = [];

        BuilderEventTestClass::composing(function () use (&$order) {
            $order[] = 'first';
        });

        BuilderEventTestClass::composing(function () use (&$order) {
            $order[] = 'second';
        });

        $obj = new BuilderEventTestClass;
        $obj->triggerComposing();

        $this->assertSame(['first', 'second'], $order);
    }

    public function test_format_event_key(): void
    {
        $reflection = new \ReflectionMethod(BuilderEventTestClass::class, 'formatEventKey');

        $key = $reflection->invoke(null, 'builder:resolving');

        $this->assertSame(BuilderEventTestClass::class.':builder:resolving', $key);
    }
}
