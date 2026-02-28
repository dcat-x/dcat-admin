<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field;
use Dcat\Admin\Form\Field\Html;
use Dcat\Admin\Tests\TestCase;

class HtmlTest extends TestCase
{
    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    // -------------------------------------------------------
    // instanceof & construction
    // -------------------------------------------------------

    public function test_is_instance_of_field(): void
    {
        $field = new Html('<p>Hello</p>', []);

        $this->assertInstanceOf(Field::class, $field);
    }

    public function test_constructor_sets_html(): void
    {
        $field = new Html('<div>Content</div>', []);

        $html = $this->getProtectedProperty($field, 'html');

        $this->assertSame('<div>Content</div>', $html);
    }

    public function test_constructor_sets_label_from_arguments(): void
    {
        $field = new Html('<p>Test</p>', ['My Label']);

        $label = $this->getProtectedProperty($field, 'label');

        $this->assertSame('My Label', $label);
    }

    public function test_constructor_with_empty_arguments(): void
    {
        $field = new Html('<p>Test</p>', []);

        $label = $this->getProtectedProperty($field, 'label');

        $this->assertNull($label);
    }

    // -------------------------------------------------------
    // plain()
    // -------------------------------------------------------

    public function test_plain_sets_plain_flag(): void
    {
        $field = new Html('<p>Test</p>', []);

        $result = $field->plain();

        $this->assertSame($field, $result);
        $this->assertTrue($this->getProtectedProperty($field, 'plain'));
    }

    public function test_render_with_plain_returns_raw_html(): void
    {
        $field = new Html('<strong>Bold</strong>', []);
        $field->plain();

        $result = $field->render();

        $this->assertSame('<strong>Bold</strong>', $result);
    }

    // -------------------------------------------------------
    // render()
    // -------------------------------------------------------

    public function test_render_without_plain_wraps_in_form_group(): void
    {
        $field = new Html('<p>Content</p>', ['Label']);

        $result = $field->render();

        $this->assertStringContainsString('form-group', $result);
        $this->assertStringContainsString('Label', $result);
        $this->assertStringContainsString('<p>Content</p>', $result);
    }

    public function test_render_with_closure_html(): void
    {
        $field = new Html(function () {
            return '<span>From Closure</span>';
        }, []);
        $field->plain();

        $result = $field->render();

        $this->assertStringContainsString('From Closure', $result);
    }
}
