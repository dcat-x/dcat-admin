<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Layout;

use Dcat\Admin\Layout\Navbar;
use Dcat\Admin\Tests\TestCase;

class NavbarTest extends TestCase
{
    protected function getElements(Navbar $navbar): array
    {
        return (new \ReflectionProperty(Navbar::class, 'elements'))->getValue($navbar);
    }

    public function test_constructor_initializes_left_and_right_collections(): void
    {
        $navbar = new Navbar;

        $elements = $this->getElements($navbar);

        $this->assertTrue(($elements['left'] ?? collect())->isEmpty());
        $this->assertTrue(($elements['right'] ?? collect())->isEmpty());
    }

    public function test_left_adds_element_to_left_collection(): void
    {
        $navbar = new Navbar;

        $navbar->left('Left Element');

        $elements = $this->getElements($navbar);

        $this->assertCount(1, $elements['left']);
        $this->assertSame('Left Element', $elements['left']->first());
    }

    public function test_right_adds_element_to_right_collection(): void
    {
        $navbar = new Navbar;

        $navbar->right('Right Element');

        $elements = $this->getElements($navbar);

        $this->assertCount(1, $elements['right']);
        $this->assertSame('Right Element', $elements['right']->first());
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

        $elements = $this->getElements($navbar);

        $this->assertCount(3, $elements['left']);
    }

    public function test_right_can_add_multiple_elements(): void
    {
        $navbar = new Navbar;

        $navbar->right('First');
        $navbar->right('Second');

        $elements = $this->getElements($navbar);

        $this->assertCount(2, $elements['right']);
    }

    public function test_left_and_right_are_independent(): void
    {
        $navbar = new Navbar;

        $navbar->left('Left Item');
        $navbar->right('Right Item');

        $elements = $this->getElements($navbar);

        $this->assertCount(1, $elements['left']);
        $this->assertCount(1, $elements['right']);
    }

    public function test_render_right_returns_string_elements(): void
    {
        $navbar = new Navbar;

        $navbar->right('Hello');
        $navbar->right(' World');

        $html = $navbar->render('right');

        $this->assertSame('Hello World', $html);
    }

    public function test_render_left_returns_string_elements(): void
    {
        $navbar = new Navbar;

        $navbar->left('Left');
        $navbar->left(' Side');

        $html = $navbar->render('left');

        $this->assertSame('Left Side', $html);
    }

    public function test_render_empty_part_returns_empty_string(): void
    {
        $navbar = new Navbar;

        $html = $navbar->render('right');

        $this->assertSame('', $html);
    }

    public function test_render_defaults_to_right(): void
    {
        $navbar = new Navbar;

        $navbar->right('RightContent');

        $html = $navbar->render();

        $this->assertSame('RightContent', $html);
    }

    public function test_chaining_left_and_right(): void
    {
        $navbar = new Navbar;

        $navbar->left('L1')->left('L2')->right('R1')->right('R2');

        $elements = $this->getElements($navbar);

        $this->assertCount(2, $elements['left']);
        $this->assertCount(2, $elements['right']);
    }
}
