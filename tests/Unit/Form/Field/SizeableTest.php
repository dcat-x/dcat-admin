<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\Sizeable;
use Dcat\Admin\Tests\TestCase;

class SizeableTest extends TestCase
{
    protected function createSizeableUser(): object
    {
        return new class
        {
            use Sizeable;

            protected $elementClass = [];

            protected $labelClass = [];

            public function addElementClass($class)
            {
                $this->elementClass[] = $class;
            }

            public function setLabelClass($class)
            {
                $this->labelClass[] = $class;
            }

            public function getElementClass()
            {
                return $this->elementClass;
            }

            public function getLabelClass()
            {
                return $this->labelClass;
            }
        };
    }

    public function test_small_sets_sm_size(): void
    {
        $user = $this->createSizeableUser();
        $result = $user->small();

        $this->assertSame($user, $result);

        $reflection = new \ReflectionProperty($user, 'size');
        $reflection->setAccessible(true);
        $this->assertSame('sm', $reflection->getValue($user));
    }

    public function test_large_sets_lg_size(): void
    {
        $user = $this->createSizeableUser();
        $result = $user->large();

        $this->assertSame($user, $result);

        $reflection = new \ReflectionProperty($user, 'size');
        $reflection->setAccessible(true);
        $this->assertSame('lg', $reflection->getValue($user));
    }

    public function test_size_sets_custom_value(): void
    {
        $user = $this->createSizeableUser();
        $result = $user->size('xl');

        $this->assertSame($user, $result);

        $reflection = new \ReflectionProperty($user, 'size');
        $reflection->setAccessible(true);
        $this->assertSame('xl', $reflection->getValue($user));
    }

    public function test_size_can_be_null(): void
    {
        $user = $this->createSizeableUser();
        $user->size(null);

        $reflection = new \ReflectionProperty($user, 'size');
        $reflection->setAccessible(true);
        $this->assertNull($reflection->getValue($user));
    }

    public function test_init_size_adds_classes(): void
    {
        $user = $this->createSizeableUser();
        $user->small();

        $reflection = new \ReflectionMethod($user, 'initSize');
        $reflection->setAccessible(true);
        $reflection->invoke($user);

        $this->assertContains('form-control-sm', $user->getElementClass());
        $this->assertContains('control-label-sm', $user->getLabelClass());
    }

    public function test_init_size_does_nothing_without_size(): void
    {
        $user = $this->createSizeableUser();

        $reflection = new \ReflectionMethod($user, 'initSize');
        $reflection->setAccessible(true);
        $reflection->invoke($user);

        $this->assertEmpty($user->getElementClass());
        $this->assertEmpty($user->getLabelClass());
    }

    public function test_default_size_is_empty_string(): void
    {
        $user = $this->createSizeableUser();

        $reflection = new \ReflectionProperty($user, 'size');
        $reflection->setAccessible(true);
        $this->assertSame('', $reflection->getValue($user));
    }
}
