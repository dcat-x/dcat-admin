<?php

namespace Dcat\Admin\Tests\Unit\Form;

use Dcat\Admin\Form\Field;
use Dcat\Admin\Form\ResolveField;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class ResolveFieldUser
{
    use ResolveField;

    // Expose the protected method for testing
    public function testCallResolvingFieldCallbacks(Field $field)
    {
        $this->callResolvingFieldCallbacks($field);
    }
}

class ResolveFieldTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_resolving_field_adds_callback(): void
    {
        $user = new ResolveFieldUser;
        $result = $user->resolvingField(function () {});
        $this->assertSame($user, $result);
    }

    public function test_resolving_field_accumulates_callbacks(): void
    {
        $user = new ResolveFieldUser;
        $user->resolvingField(function () {});
        $user->resolvingField(function () {});

        $ref = new \ReflectionProperty($user, 'resolvingFieldCallbacks');
        $ref->setAccessible(true);
        $this->assertCount(2, $ref->getValue($user));
    }

    public function test_set_resolving_field_callbacks_replaces(): void
    {
        $user = new ResolveFieldUser;
        $user->resolvingField(function () {});
        $user->resolvingField(function () {});

        $user->setResolvingFieldCallbacks([function () {}]);

        $ref = new \ReflectionProperty($user, 'resolvingFieldCallbacks');
        $ref->setAccessible(true);
        $this->assertCount(1, $ref->getValue($user));
    }

    public function test_call_resolving_field_callbacks_executes_all(): void
    {
        $user = new ResolveFieldUser;
        $executed = [];

        $user->resolvingField(function ($field, $self) use (&$executed) {
            $executed[] = 1;
        });
        $user->resolvingField(function ($field, $self) use (&$executed) {
            $executed[] = 2;
        });

        $field = Mockery::mock(Field::class);
        $user->testCallResolvingFieldCallbacks($field);

        $this->assertEquals([1, 2], $executed);
    }

    public function test_call_resolving_stops_on_false(): void
    {
        $user = new ResolveFieldUser;
        $executed = [];

        $user->resolvingField(function ($field, $self) use (&$executed) {
            $executed[] = 1;

            return false;
        });
        $user->resolvingField(function ($field, $self) use (&$executed) {
            $executed[] = 2;
        });

        $field = Mockery::mock(Field::class);
        $user->testCallResolvingFieldCallbacks($field);

        $this->assertEquals([1], $executed);
    }

    public function test_callback_receives_field_and_self(): void
    {
        $user = new ResolveFieldUser;
        $receivedField = null;
        $receivedSelf = null;

        $user->resolvingField(function ($field, $self) use (&$receivedField, &$receivedSelf) {
            $receivedField = $field;
            $receivedSelf = $self;
        });

        $field = Mockery::mock(Field::class);
        $user->testCallResolvingFieldCallbacks($field);

        $this->assertSame($field, $receivedField);
        $this->assertSame($user, $receivedSelf);
    }

    public function test_empty_callbacks_does_nothing(): void
    {
        $user = new ResolveFieldUser;
        $field = Mockery::mock(Field::class);

        // Should not throw
        $user->testCallResolvingFieldCallbacks($field);
        $this->addToAssertionCount(1);
    }
}
