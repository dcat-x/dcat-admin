<?php

namespace Dcat\Admin\Tests\Unit\Console;

use Dcat\Admin\Console\ActionCommand;
use Dcat\Admin\Console\GeneratorCommand;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class ActionCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_class_exists(): void
    {
        $this->assertTrue(class_exists(ActionCommand::class));
    }

    public function test_extends_generator_command(): void
    {
        $this->assertTrue(is_subclass_of(ActionCommand::class, GeneratorCommand::class));
    }

    public function test_signature_default_value(): void
    {
        $ref = new \ReflectionProperty(ActionCommand::class, 'signature');
        $defaultValue = $ref->getDefaultValue();

        $this->assertEquals('admin:action', $defaultValue);
    }

    public function test_description_default_value(): void
    {
        $ref = new \ReflectionProperty(ActionCommand::class, 'description');
        $defaultValue = $ref->getDefaultValue();

        $this->assertEquals('Make a admin action', $defaultValue);
    }

    public function test_has_choice_property(): void
    {
        $this->assertTrue(property_exists(ActionCommand::class, 'choice'));

        $ref = new \ReflectionProperty(ActionCommand::class, 'choice');

        $this->assertTrue($ref->isProtected());
    }

    public function test_has_class_name_property(): void
    {
        $this->assertTrue(property_exists(ActionCommand::class, 'className'));

        $ref = new \ReflectionProperty(ActionCommand::class, 'className');

        $this->assertTrue($ref->isProtected());
    }

    public function test_has_namespace_property(): void
    {
        $this->assertTrue(property_exists(ActionCommand::class, 'namespace'));

        $ref = new \ReflectionProperty(ActionCommand::class, 'namespace');

        $this->assertTrue($ref->isProtected());
    }

    public function test_has_namespace_map_property(): void
    {
        $this->assertTrue(property_exists(ActionCommand::class, 'namespaceMap'));

        $ref = new \ReflectionProperty(ActionCommand::class, 'namespaceMap');

        $this->assertTrue($ref->isProtected());
        $this->assertTrue($ref->hasDefaultValue());

        $defaultValue = $ref->getDefaultValue();
        $this->assertIsArray($defaultValue);

        $expectedKeys = ['grid-batch', 'grid-row', 'grid-tool', 'form-tool', 'show-tool', 'tree-row', 'tree-tool'];
        foreach ($expectedKeys as $key) {
            $this->assertArrayHasKey($key, $defaultValue, "namespaceMap should have key '{$key}'");
        }
    }

    public function test_namespace_map_values(): void
    {
        $ref = new \ReflectionProperty(ActionCommand::class, 'namespaceMap');
        $map = $ref->getDefaultValue();

        $this->assertEquals('Grid', $map['grid-batch']);
        $this->assertEquals('Grid', $map['grid-row']);
        $this->assertEquals('Grid', $map['grid-tool']);
        $this->assertEquals('Form', $map['form-tool']);
        $this->assertEquals('Show', $map['show-tool']);
        $this->assertEquals('Tree', $map['tree-row']);
        $this->assertEquals('Tree', $map['tree-tool']);
    }

    public function test_has_required_methods(): void
    {
        $methods = [
            'handle',
            'actionTyps',
            'replaceClass',
            'getStub',
            'getDefaultNamespace',
            'getNameInput',
        ];

        foreach ($methods as $method) {
            $this->assertTrue(
                method_exists(ActionCommand::class, $method),
                "ActionCommand should have method '{$method}'"
            );
        }
    }

    public function test_action_typs_is_protected(): void
    {
        $ref = new \ReflectionMethod(ActionCommand::class, 'actionTyps');

        $this->assertTrue($ref->isProtected());
    }

    public function test_get_stub_is_public(): void
    {
        $ref = new \ReflectionMethod(ActionCommand::class, 'getStub');

        $this->assertTrue($ref->isPublic());
    }
}
