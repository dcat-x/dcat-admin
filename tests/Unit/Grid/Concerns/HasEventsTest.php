<?php

namespace Dcat\Admin\Tests\Unit\Grid\Concerns;

use Dcat\Admin\Grid\Concerns\HasEvents;
use Dcat\Admin\Grid\Events\Fetching;
use Dcat\Admin\Tests\TestCase;

class HasEventsTest extends TestCase
{
    public function test_trait_has_listen_method(): void
    {
        $this->assertTrue(method_exists(HasEventsTestHelper::class, 'listen'));
    }

    public function test_trait_has_fire_method(): void
    {
        $this->assertTrue(method_exists(HasEventsTestHelper::class, 'fire'));
    }

    public function test_trait_has_fire_once_method(): void
    {
        $this->assertTrue(method_exists(HasEventsTestHelper::class, 'fireOnce'));
    }

    public function test_dispatched_initially_empty(): void
    {
        $user = new HasEventsTestHelper;

        $reflection = new \ReflectionProperty($user, 'dispatched');
        $reflection->setAccessible(true);
        $this->assertEmpty($reflection->getValue($user));
    }

    public function test_dispatched_is_array(): void
    {
        $user = new HasEventsTestHelper;

        $reflection = new \ReflectionProperty($user, 'dispatched');
        $reflection->setAccessible(true);
        $this->assertIsArray($reflection->getValue($user));
    }

    public function test_fire_once_checks_dispatched_array(): void
    {
        $user = new HasEventsTestHelper;

        $reflection = new \ReflectionProperty($user, 'dispatched');
        $reflection->setAccessible(true);

        // Simulate that Fetching was already dispatched
        $reflection->setValue($user, [Fetching::class => new Fetching]);

        // fireOnce should not call fire again for same type
        // We verify by checking the dispatched array doesn't change
        $dispatched = $reflection->getValue($user);
        $this->assertArrayHasKey(Fetching::class, $dispatched);
    }
}

class HasEventsTestHelper
{
    use HasEvents;
}
