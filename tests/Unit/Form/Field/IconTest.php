<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\Icon;
use Dcat\Admin\Form\Field\Text;
use Dcat\Admin\Tests\TestCase;

class IconTest extends TestCase
{
    protected function getProtectedProperty(object $object, string $property)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    // -------------------------------------------------------
    // instanceof & inheritance
    // -------------------------------------------------------

    public function test_is_instance_of_text(): void
    {
        $field = new Icon('icon', ['Icon']);

        $this->assertInstanceOf(Text::class, $field);
    }

    // -------------------------------------------------------
    // static assets
    // -------------------------------------------------------

    public function test_js_static_property(): void
    {
        $this->assertSame('@fontawesome-iconpicker', Icon::$js);
    }

    public function test_css_static_property(): void
    {
        $this->assertSame('@fontawesome-iconpicker', Icon::$css);
    }

    // -------------------------------------------------------
    // construction
    // -------------------------------------------------------

    public function test_constructor_sets_column(): void
    {
        $field = new Icon('icon_name', ['Icon Picker']);

        $column = $this->getProtectedProperty($field, 'column');

        $this->assertSame('icon_name', $column);
    }

    public function test_constructor_sets_label(): void
    {
        $field = new Icon('icon', ['My Icon']);

        $label = $this->getProtectedProperty($field, 'label');

        $this->assertSame('My Icon', $label);
    }
}
