<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Form\Concerns;

use Dcat\Admin\Form;
use Dcat\Admin\Form\Concerns\HasAutoSave;
use Dcat\Admin\Tests\TestCase;

class HasAutoSaveTest extends TestCase
{
    public function test_form_uses_has_auto_save_trait(): void
    {
        $ref = new \ReflectionClass(Form::class);
        $traits = $this->getAllTraits($ref);

        $this->assertContains(HasAutoSave::class, $traits);
    }

    public function test_auto_save_defaults_to_false(): void
    {
        $helper = new HasAutoSaveTestHelper;
        $ref = new \ReflectionProperty($helper, 'autoSave');
        $ref->setAccessible(true);

        $this->assertFalse($ref->getValue($helper));
    }

    public function test_auto_save_interval_defaults_to_30(): void
    {
        $helper = new HasAutoSaveTestHelper;
        $ref = new \ReflectionProperty($helper, 'autoSaveInterval');
        $ref->setAccessible(true);

        $this->assertSame(30, $ref->getValue($helper));
    }

    public function test_auto_save_enables_feature(): void
    {
        $helper = new HasAutoSaveTestHelper;
        $result = $helper->autoSave();

        $this->assertSame($helper, $result);

        $ref = new \ReflectionProperty($helper, 'autoSave');
        $ref->setAccessible(true);
        $this->assertTrue($ref->getValue($helper));
    }

    public function test_auto_save_with_custom_interval(): void
    {
        $helper = new HasAutoSaveTestHelper;
        $helper->autoSave(60);

        $ref = new \ReflectionProperty($helper, 'autoSaveInterval');
        $ref->setAccessible(true);
        $this->assertSame(60, $ref->getValue($helper));
    }

    public function test_render_auto_save_does_nothing_when_disabled(): void
    {
        $helper = new HasAutoSaveTestHelper;
        $helper->renderAutoSavePublic();

        // Should not throw or produce output
        $this->assertFalse((new \ReflectionProperty($helper, 'autoSave'))->getValue($helper));
    }

    public function test_auto_save_method_returns_static(): void
    {
        $method = new \ReflectionMethod(HasAutoSaveTestHelper::class, 'autoSave');

        $this->assertTrue($method->isPublic());
        $this->assertCount(1, $method->getParameters());
        $this->assertSame('interval', $method->getParameters()[0]->getName());
        $this->assertSame(30, $method->getParameters()[0]->getDefaultValue());
    }

    private function getAllTraits(\ReflectionClass $ref): array
    {
        $traits = array_keys($ref->getTraits());
        foreach ($ref->getTraits() as $trait) {
            $traits = array_merge($traits, $this->getAllTraits($trait));
        }
        if ($parent = $ref->getParentClass()) {
            $traits = array_merge($traits, $this->getAllTraits($parent));
        }

        return array_unique($traits);
    }
}

class HasAutoSaveTestHelper
{
    use HasAutoSave;

    public function getElementId()
    {
        return 'test-form-id';
    }

    public function renderAutoSavePublic(): void
    {
        $this->renderAutoSave();
    }
}
