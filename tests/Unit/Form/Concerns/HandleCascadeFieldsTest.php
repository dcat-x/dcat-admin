<?php

namespace Dcat\Admin\Tests\Unit\Form\Concerns;

use Dcat\Admin\Form\Concerns\HandleCascadeFields;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class HandleCascadeFieldsTestHelper
{
    use HandleCascadeFields;

    public $pushedFields = [];

    public $htmlCalls = [];

    public function pushField($field): void
    {
        $this->pushedFields[] = $field;
    }

    public function html($content)
    {
        $this->htmlCalls[] = $content;

        return $this;
    }

    public function plain()
    {
        return $this;
    }
}

class HandleCascadeFieldsTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_trait_cascade_group_method_signature(): void
    {
        $method = new \ReflectionMethod(HandleCascadeFieldsTestHelper::class, 'cascadeGroup');

        $this->assertSame(2, $method->getNumberOfParameters());
    }

    public function test_cascade_group_method_exists(): void
    {
        $method = new \ReflectionMethod(HandleCascadeFieldsTestHelper::class, 'cascadeGroup');

        $this->assertTrue($method->isPublic());
    }

    public function test_cascade_group_requires_closure_and_array(): void
    {
        $ref = new \ReflectionMethod(HandleCascadeFieldsTestHelper::class, 'cascadeGroup');
        $params = $ref->getParameters();

        $this->assertCount(2, $params);
        $this->assertSame('closure', $params[0]->getName());
        $this->assertSame('dependency', $params[1]->getName());
    }

    public function test_trait_can_be_used_in_class(): void
    {
        $helper = new HandleCascadeFieldsTestHelper;

        $this->assertInstanceOf(HandleCascadeFieldsTestHelper::class, $helper);
    }

    public function test_cascade_group_parameters(): void
    {
        $ref = new \ReflectionMethod(HandleCascadeFieldsTestHelper::class, 'cascadeGroup');
        $params = $ref->getParameters();

        $closureType = $params[0]->getType();
        $this->assertNotNull($closureType);
        $this->assertSame('Closure', $closureType->getName());

        $arrayType = $params[1]->getType();
        $this->assertNotNull($arrayType);
        $this->assertSame('array', $arrayType->getName());
    }
}
