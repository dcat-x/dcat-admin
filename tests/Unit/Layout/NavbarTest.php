<?php

namespace Dcat\Admin\Tests\Unit\Layout;

use Dcat\Admin\Layout\Navbar;
use Dcat\Admin\Tests\TestCase;

class NavbarTest extends TestCase
{
    public function test_constructor_initializes_left_and_right_collections(): void
    {
        $navbar = new Navbar;

        $elements = (new \ReflectionProperty(Navbar::class, 'elements'))->getValue($navbar);

        $this->assertArrayHasKey('left', $elements);
        $this->assertArrayHasKey('right', $elements);
        $this->assertTrue($elements['left']->isEmpty());
        $this->assertTrue($elements['right']->isEmpty());
    }

    public function test_left_adds_element_to_left_collection(): void
    {
        $navbar = new Navbar;

        $navbar->left('Left Element');

        $elements = (new \ReflectionProperty(Navbar::class, 'elements'))->getValue($navbar);

        $this->assertCount(1, $elements['left']);
        $this->assertEquals('Left Element', $elements['left']->first());
    }

    public function test_right_adds_element_to_right_collection(): void
    {
        $navbar = new Navbar;

        $navbar->right('Right Element');

        $elements = (new \ReflectionProperty(Navbar::class, 'elements'))->getValue($navbar);

        $this->assertCount(1, $elements['right']);
        $this->assertEquals('Right Element', $elements['right']->first());
    }

    public function test_left_returns_self_for_chaining(): void
    {
        $navbar = new Navbar;

        $result = $navbar->left('element');

        $this->assertSame($navbar, $result);
    }

    public function test_right_returns_self_for_chaining(): void
    {
        $navbar = new Navbar;

        $result = $navbar->right('element');

        $this->assertSame($navbar, $result);
    }

    public function test_left_can_add_multiple_elements(): void
    {
        $navbar = new Navbar;

        $navbar->left('First');
        $navbar->left('Second');
        $navbar->left('Third');

        $elements = (new \ReflectionProperty(Navbar::class, 'elements'))->getValue($navbar);

        $this->assertCount(3, $elements['left']);
    }

    public function test_right_can_add_multiple_elements(): void
    {
        $navbar = new Navbar;

        $navbar->right('First');
        $navbar->right('Second');

        $elements = (new \ReflectionProperty(Navbar::class, 'elements'))->getValue($navbar);

        $this->assertCount(2, $elements['right']);
    }

    public function test_left_and_right_are_independent(): void
    {
        $navbar = new Navbar;

        $navbar->left('Left Item');
        $navbar->right('Right Item');

        $elements = (new \ReflectionProperty(Navbar::class, 'elements'))->getValue($navbar);

        $this->assertCount(1, $elements['left']);
        $this->assertCount(1, $elements['right']);
    }

    public function test_render_right_returns_string_elements(): void
    {
        $navbar = new Navbar;

        $navbar->right('Hello');
        $navbar->right(' World');

        $html = $navbar->render('right');

        $this->assertEquals('Hello World', $html);
    }

    public function test_render_left_returns_string_elements(): void
    {
        $navbar = new Navbar;

        $navbar->left('Left');
        $navbar->left(' Side');

        $html = $navbar->render('left');

        $this->assertEquals('Left Side', $html);
    }

    public function test_render_empty_part_returns_empty_string(): void
    {
        $navbar = new Navbar;

        $html = $navbar->render('right');

        $this->assertEquals('', $html);
    }

    public function test_render_defaults_to_right(): void
    {
        $navbar = new Navbar;

        $navbar->right('RightContent');

        $html = $navbar->render();

        $this->assertEquals('RightContent', $html);
    }

    public function test_chaining_left_and_right(): void
    {
        $navbar = new Navbar;

        $navbar->left('L1')->left('L2')->right('R1')->right('R2');

        $elements = (new \ReflectionProperty(Navbar::class, 'elements'))->getValue($navbar);

        $this->assertCount(2, $elements['left']);
        $this->assertCount(2, $elements['right']);
    }
}
