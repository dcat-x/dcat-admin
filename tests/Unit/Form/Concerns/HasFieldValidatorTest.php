<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Form\Concerns;

use Dcat\Admin\Form\Concerns\HasFieldValidator;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class HasFieldValidatorTestHelper
{
    use HasFieldValidator;

    public $column = 'test';

    public $label = 'Test';

    public $form = null;

    protected function isCreating()
    {
        return false;
    }

    protected function isEditing()
    {
        return false;
    }
}

class HasFieldValidatorTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function createHelper(): HasFieldValidatorTestHelper
    {
        return new HasFieldValidatorTestHelper;
    }

    public function test_rules_sets_array_rules(): void
    {
        $helper = $this->createHelper();

        $helper->rules(['required', 'string']);

        $ref = new \ReflectionProperty($helper, 'rules');
        $ref->setAccessible(true);
        $rules = $ref->getValue($helper);

        $this->assertIsArray($rules);
        $this->assertContains('required', $rules);
        $this->assertContains('string', $rules);
    }

    public function test_rules_sets_string_rules(): void
    {
        $helper = $this->createHelper();

        $helper->rules('required|email');

        $ref = new \ReflectionProperty($helper, 'rules');
        $ref->setAccessible(true);
        $rules = $ref->getValue($helper);

        $this->assertIsArray($rules);
        $this->assertContains('required', $rules);
        $this->assertContains('email', $rules);
    }

    public function test_rules_returns_self(): void
    {
        $helper = $this->createHelper();

        $result = $helper->rules(['required']);

        $this->assertSame($helper, $result);
    }

    public function test_update_rules_sets_rules(): void
    {
        $helper = $this->createHelper();

        $helper->updateRules(['required', 'string']);

        $ref = new \ReflectionProperty($helper, 'updateRules');
        $ref->setAccessible(true);
        $rules = $ref->getValue($helper);

        $this->assertIsArray($rules);
        $this->assertContains('required', $rules);
    }

    public function test_update_rules_returns_self(): void
    {
        $helper = $this->createHelper();

        $result = $helper->updateRules(['required']);

        $this->assertSame($helper, $result);
    }

    public function test_creation_rules_sets_rules(): void
    {
        $helper = $this->createHelper();

        $helper->creationRules(['required', 'unique:users']);

        $ref = new \ReflectionProperty($helper, 'creationRules');
        $ref->setAccessible(true);
        $rules = $ref->getValue($helper);

        $this->assertIsArray($rules);
        $this->assertContains('required', $rules);
    }

    public function test_creation_rules_returns_self(): void
    {
        $helper = $this->createHelper();

        $result = $helper->creationRules(['required']);

        $this->assertSame($helper, $result);
    }

    public function test_remove_rule_removes_from_rules(): void
    {
        $helper = $this->createHelper();

        $helper->rules(['required', 'string', 'max:255']);
        $helper->removeRule('string');

        $this->assertFalse($helper->hasRule('string'));
    }

    public function test_has_rule_returns_true_when_exists(): void
    {
        $helper = $this->createHelper();

        $helper->rules(['required', 'email']);

        $this->assertTrue($helper->hasRule('required'));
    }

    public function test_has_rule_returns_false_when_not_exists(): void
    {
        $helper = $this->createHelper();

        $helper->rules(['required']);

        $this->assertFalse($helper->hasRule('email'));
    }
}
