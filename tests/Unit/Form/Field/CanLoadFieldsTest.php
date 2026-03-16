<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\CanLoadFields;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class CanLoadFieldsTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    // -------------------------------------------------------
    // Trait existence
    // -------------------------------------------------------

    public function test_trait_exists(): void
    {
        $this->assertTrue(trait_exists(CanLoadFields::class));
    }

    // -------------------------------------------------------
    // Method existence
    // -------------------------------------------------------

    public function test_load_method_signature(): void
    {
        $method = new \ReflectionMethod(CanLoadFields::class, 'load');

        $this->assertTrue($method->isPublic());
        $this->assertSame(4, $method->getNumberOfParameters());
    }

    public function test_loads_method_signature(): void
    {
        $method = new \ReflectionMethod(CanLoadFields::class, 'loads');

        $this->assertTrue($method->isPublic());
        $this->assertSame(4, $method->getNumberOfParameters());
    }

    // -------------------------------------------------------
    // Method visibility checks
    // -------------------------------------------------------

    public function test_load_is_public(): void
    {
        $method = new \ReflectionMethod(CanLoadFields::class, 'load');
        $this->assertTrue($method->isPublic());
    }

    public function test_loads_is_public(): void
    {
        $method = new \ReflectionMethod(CanLoadFields::class, 'loads');
        $this->assertTrue($method->isPublic());
    }

    // -------------------------------------------------------
    // Method parameter checks
    // -------------------------------------------------------

    public function test_load_has_expected_parameters(): void
    {
        $method = new \ReflectionMethod(CanLoadFields::class, 'load');
        $params = $method->getParameters();

        $this->assertCount(4, $params);
        $this->assertSame('field', $params[0]->getName());
        $this->assertSame('sourceUrl', $params[1]->getName());
        $this->assertSame('idField', $params[2]->getName());
        $this->assertSame('textField', $params[3]->getName());
    }

    public function test_loads_has_expected_parameters(): void
    {
        $method = new \ReflectionMethod(CanLoadFields::class, 'loads');
        $params = $method->getParameters();

        $this->assertCount(4, $params);
        $this->assertSame('fields', $params[0]->getName());
        $this->assertSame('sourceUrls', $params[1]->getName());
        $this->assertSame('idField', $params[2]->getName());
        $this->assertSame('textField', $params[3]->getName());
    }

    public function test_load_id_field_default_is_id(): void
    {
        $method = new \ReflectionMethod(CanLoadFields::class, 'load');
        $params = $method->getParameters();

        $this->assertTrue($params[2]->isDefaultValueAvailable());
        $this->assertSame('id', $params[2]->getDefaultValue());
    }

    public function test_load_text_field_default_is_text(): void
    {
        $method = new \ReflectionMethod(CanLoadFields::class, 'load');
        $params = $method->getParameters();

        $this->assertTrue($params[3]->isDefaultValueAvailable());
        $this->assertSame('text', $params[3]->getDefaultValue());
    }

    public function test_loads_fields_default_is_empty_array(): void
    {
        $method = new \ReflectionMethod(CanLoadFields::class, 'loads');
        $params = $method->getParameters();

        $this->assertTrue($params[0]->isDefaultValueAvailable());
        $this->assertSame([], $params[0]->getDefaultValue());
    }

    public function test_loads_source_urls_default_is_empty_array(): void
    {
        $method = new \ReflectionMethod(CanLoadFields::class, 'loads');
        $params = $method->getParameters();

        $this->assertTrue($params[1]->isDefaultValueAvailable());
        $this->assertSame([], $params[1]->getDefaultValue());
    }
}
