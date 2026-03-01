<?php

namespace Dcat\Admin\Tests\Unit\Scaffold;

use Dcat\Admin\Scaffold\FormCreator;
use Dcat\Admin\Tests\TestCase;

class FormCreatorTest extends TestCase
{
    protected function createFormCreator(): object
    {
        return new class
        {
            use FormCreator {
                generateForm as public;
            }
        };
    }

    public function test_generate_form_with_primary_key(): void
    {
        $creator = $this->createFormCreator();
        $result = $creator->generateForm('id', []);

        $this->assertStringContainsString("\$form->display('id')", $result);
    }

    public function test_generate_form_with_fields(): void
    {
        $creator = $this->createFormCreator();
        $fields = [
            ['name' => 'title'],
            ['name' => 'content'],
        ];
        $result = $creator->generateForm('id', $fields);

        $this->assertStringContainsString("\$form->text('title')", $result);
        $this->assertStringContainsString("\$form->text('content')", $result);
    }

    public function test_generate_form_skips_primary_key_field(): void
    {
        $creator = $this->createFormCreator();
        $fields = [
            ['name' => 'id'],
            ['name' => 'title'],
        ];
        $result = $creator->generateForm('id', $fields);

        $this->assertStringContainsString("\$form->display('id')", $result);
        $this->assertStringNotContainsString("\$form->text('id')", $result);
        $this->assertStringContainsString("\$form->text('title')", $result);
    }

    public function test_generate_form_skips_empty_name_fields(): void
    {
        $creator = $this->createFormCreator();
        $fields = [
            ['name' => ''],
            ['name' => 'title'],
        ];
        $result = $creator->generateForm('id', $fields);

        $this->assertStringContainsString("\$form->text('title')", $result);
    }

    public function test_generate_form_with_timestamps(): void
    {
        $creator = $this->createFormCreator();
        $result = $creator->generateForm('id', [], true);

        $this->assertStringContainsString("\$form->display('created_at')", $result);
        $this->assertStringContainsString("\$form->display('updated_at')", $result);
    }

    public function test_generate_form_without_timestamps(): void
    {
        $creator = $this->createFormCreator();
        $result = $creator->generateForm('id', [], false);

        $this->assertStringNotContainsString('created_at', $result);
        $this->assertStringNotContainsString('updated_at', $result);
    }
}
