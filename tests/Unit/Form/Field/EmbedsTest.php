<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Contracts\FieldsCollection;
use Dcat\Admin\Form\Field\Embeds;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class EmbedsTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    protected function invokeProtectedMethod(object $object, string $method, array $arguments = [])
    {
        $reflection = new \ReflectionMethod($object, $method);
        $reflection->setAccessible(true);

        return $reflection->invokeArgs($object, $arguments);
    }

    public function test_implements_fields_collection(): void
    {
        $reflection = new \ReflectionClass(Embeds::class);

        $this->assertTrue($reflection->implementsInterface(FieldsCollection::class));
    }

    public function test_builder_default_is_null(): void
    {
        $reflection = new \ReflectionProperty(Embeds::class, 'builder');
        $reflection->setAccessible(true);

        $this->assertNull($reflection->getDefaultValue());
    }

    public function test_constructor_with_single_argument_sets_builder(): void
    {
        $builder = static function (): void {};

        $field = new Embeds('extra', [$builder]);

        $this->assertSame($builder, $this->getProtectedProperty($field, 'builder'));
    }

    public function test_reset_input_key_renames_fields_for_array_columns(): void
    {
        $field = new Embeds('extra', [static function (): void {}]);

        $input = [
            'extra' => [
                'start_at' => '2026-01-01',
                'end_at' => '2026-01-31',
                'note' => 'keep',
            ],
        ];

        $field->resetInputKey($input, ['start' => 'start_at', 'end' => 'end_at']);

        $this->assertContains('start_atstart', array_keys($input['extra']));
        $this->assertContains('end_atend', array_keys($input['extra']));
        $this->assertArrayNotHasKey('start_at', $input['extra']);
        $this->assertArrayNotHasKey('end_at', $input['extra']);
        $this->assertSame('keep', $input['extra']['note']);
    }

    public function test_get_validator_returns_false_when_input_missing_column(): void
    {
        $field = new Embeds('extra', [static function (): void {}]);

        $validator = $field->getValidator(['other' => ['k' => 'v']]);

        $this->assertFalse($validator);
    }

    public function test_format_validation_messages_prefixes_column_name(): void
    {
        $field = new Embeds('extra', [static function (): void {}]);

        $messages = $this->invokeProtectedMethod($field, 'formatValidationMessages', [
            [],
            ['name.required' => 'Name is required'],
        ]);

        $this->assertSame(['extra.name.required' => 'Name is required'], $messages);
    }

    public function test_format_validation_attribute_for_string_column(): void
    {
        $field = new Embeds('extra', [static function (): void {}]);

        $attributes = $this->invokeProtectedMethod($field, 'formatValidationAttribute', [
            ['extra' => ['name' => 'cooper']],
            'Name',
            'name',
        ]);

        $this->assertSame(['extra.name' => 'Name'], $attributes);
    }
}
