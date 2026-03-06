<?php

namespace Dcat\Admin\Tests\Unit\Form;

use Dcat\Admin\Form;
use Dcat\Admin\Form\EmbeddedForm;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Support\Collection;
use Mockery;

class EmbeddedFormTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_constructor_creates_instance(): void
    {
        $form = new EmbeddedForm('profile');
        $this->assertInstanceOf(EmbeddedForm::class, $form);
    }

    public function test_fields_returns_empty_collection(): void
    {
        $form = new EmbeddedForm('profile');
        $fields = $form->fields();
        $this->assertInstanceOf(Collection::class, $fields);
        $this->assertTrue($fields->isEmpty());
    }

    public function test_set_parent_returns_this(): void
    {
        $form = new EmbeddedForm('profile');
        $parent = Mockery::mock(Form::class);
        $result = $form->setParent($parent);
        $this->assertSame($form, $result);
    }

    public function test_set_original_with_array(): void
    {
        $form = new EmbeddedForm('profile');
        $result = $form->setOriginal(['name' => 'test', 'age' => 25]);
        $this->assertSame($form, $result);

        $ref = new \ReflectionProperty($form, 'original');
        $ref->setAccessible(true);
        $original = $ref->getValue($form);
        $this->assertSame('test', $original['name']);
        $this->assertSame(25, $original['age']);
    }

    public function test_set_original_with_json_string(): void
    {
        $form = new EmbeddedForm('profile');
        $form->setOriginal('{"name":"test","age":25}');

        $ref = new \ReflectionProperty($form, 'original');
        $ref->setAccessible(true);
        $original = $ref->getValue($form);
        $this->assertSame('test', $original['name']);
    }

    public function test_set_original_with_empty_returns_this(): void
    {
        $form = new EmbeddedForm('profile');
        $result = $form->setOriginal([]);
        $this->assertSame($form, $result);

        $ref = new \ReflectionProperty($form, 'original');
        $ref->setAccessible(true);
        $this->assertEmpty($ref->getValue($form));
    }

    public function test_set_original_with_null_returns_this(): void
    {
        $form = new EmbeddedForm('profile');
        $result = $form->setOriginal(null);
        $this->assertSame($form, $result);
    }

    public function test_column_stored(): void
    {
        $form = new EmbeddedForm('user_data');

        $ref = new \ReflectionProperty($form, 'column');
        $ref->setAccessible(true);
        $this->assertSame('user_data', $ref->getValue($form));
    }

    public function test_resolving_field_adds_callback(): void
    {
        $form = new EmbeddedForm('profile');
        $callback = function () {};
        $result = $form->resolvingField($callback);

        $this->assertSame($form, $result);

        $ref = new \ReflectionProperty($form, 'resolvingFieldCallbacks');
        $ref->setAccessible(true);
        $callbacks = $ref->getValue($form);
        $this->assertCount(1, $callbacks);
    }

    public function test_set_resolving_field_callbacks(): void
    {
        $form = new EmbeddedForm('profile');
        $cb1 = function () {};
        $cb2 = function () {};

        $form->setResolvingFieldCallbacks([$cb1, $cb2]);

        $ref = new \ReflectionProperty($form, 'resolvingFieldCallbacks');
        $ref->setAccessible(true);
        $this->assertCount(2, $ref->getValue($form));
    }
}
