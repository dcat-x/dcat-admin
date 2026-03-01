<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\PlainInput;
use Dcat\Admin\Tests\TestCase;

class PlainInputTest extends TestCase
{
    protected function createPlainInputUser(): object
    {
        return new class
        {
            use PlainInput;

            public $view = '';
        };
    }

    public function test_prepend_sets_value(): void
    {
        $user = $this->createPlainInputUser();
        $result = $user->prepend('$');

        $this->assertSame($user, $result);

        $reflection = new \ReflectionProperty($user, 'prepend');
        $reflection->setAccessible(true);
        $this->assertEquals('$', $reflection->getValue($user));
    }

    public function test_append_sets_value(): void
    {
        $user = $this->createPlainInputUser();
        $result = $user->append('.00');

        $this->assertSame($user, $result);

        $reflection = new \ReflectionProperty($user, 'append');
        $reflection->setAccessible(true);
        $this->assertEquals('.00', $reflection->getValue($user));
    }

    public function test_init_plain_input_sets_default_view(): void
    {
        $user = $this->createPlainInputUser();
        $user->view = '';

        $reflection = new \ReflectionMethod($user, 'initPlainInput');
        $reflection->setAccessible(true);
        $reflection->invoke($user);

        $this->assertEquals('admin::form.input', $user->view);
    }

    public function test_init_plain_input_does_not_override_existing_view(): void
    {
        $user = $this->createPlainInputUser();
        $user->view = 'custom::view';

        $reflection = new \ReflectionMethod($user, 'initPlainInput');
        $reflection->setAccessible(true);
        $reflection->invoke($user);

        $this->assertEquals('custom::view', $user->view);
    }

    public function test_prepend_and_append_default_null(): void
    {
        $user = $this->createPlainInputUser();

        $prependRef = new \ReflectionProperty($user, 'prepend');
        $prependRef->setAccessible(true);
        $this->assertNull($prependRef->getValue($user));

        $appendRef = new \ReflectionProperty($user, 'append');
        $appendRef->setAccessible(true);
        $this->assertNull($appendRef->getValue($user));
    }
}
