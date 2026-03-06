<?php

namespace Dcat\Admin\Tests\Unit\Grid\Displayers;

use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Column;
use Dcat\Admin\Grid\Displayers\AbstractDisplayer;
use Dcat\Admin\Grid\Displayers\Modal;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class ModalLazyRenderable implements LazyRenderable
{
    public static array $payload = [];

    public static function reset(): void
    {
        static::$payload = [];
    }

    public static function make()
    {
        return new static;
    }

    public function getUrl()
    {
        return '/lazy/modal';
    }

    public function render()
    {
        return '<div>lazy</div>';
    }

    public function payload(array $payload)
    {
        static::$payload = $payload;

        return $this;
    }

    public function requireAssets(): void {}

    public function passesAuthorization(): bool
    {
        return true;
    }

    public function failedAuthorization()
    {
        return 'forbidden';
    }
}

class ModalTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
        ModalLazyRenderable::reset();
    }

    protected function makeDisplayer(string $value = 'Open'): Modal
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('getKeyName')->andReturn('id');
        $grid->shouldReceive('resource')->andReturn('/admin/users');

        $column = Mockery::mock(Column::class);
        $column->shouldReceive('getName')->andReturn('name');

        return new class($value, $grid, $column, ['id' => 15]) extends Modal
        {
            public function exposeRenderButton(): string
            {
                return $this->renderButton();
            }
        };
    }

    public function test_modal_is_displayer_instance(): void
    {
        $displayer = $this->makeDisplayer();

        $this->assertInstanceOf(AbstractDisplayer::class, $displayer);
    }

    public function test_render_button_uses_default_icon_and_value(): void
    {
        $displayer = $this->makeDisplayer('Inspect');
        $button = $displayer->exposeRenderButton();

        $this->assertStringContainsString("class='fa fa-clone'", $button);
        $this->assertStringContainsString('Inspect', $button);
    }

    public function test_render_button_can_hide_icon(): void
    {
        $displayer = $this->makeDisplayer('Inspect');
        $displayer->icon('');

        $button = $displayer->exposeRenderButton();

        $this->assertStringNotContainsString("class='fa", $button);
        $this->assertStringContainsString('Inspect', $button);
    }

    public function test_display_renders_modal_with_custom_title_and_xl_size(): void
    {
        $displayer = $this->makeDisplayer('Open Detail');
        $displayer->title('User Detail');
        $displayer->xl();
        $displayer->icon('fa-user');

        $html = $displayer->display(function () {
            return '<div id="modal-body">content</div>';
        });

        $this->assertStringContainsString('fa-user', $html);
        $this->assertStringContainsString('Open Detail', $html);
        $this->assertStringContainsString('data-target="#modal-', $html);
    }

    public function test_display_supports_two_argument_signature_for_title_and_callback(): void
    {
        $displayer = $this->makeDisplayer('Open');

        $html = $displayer->display('Manual Title', function () {
            return '<span>inline content</span>';
        });

        $this->assertStringContainsString("class='fa fa-clone'", $html);
        $this->assertStringContainsString('Open</a>', $html);
    }

    public function test_display_with_lazy_renderable_class_sets_payload_key(): void
    {
        $displayer = $this->makeDisplayer('Open');

        $html = $displayer->display(ModalLazyRenderable::class);

        $this->assertSame(['key' => 15], ModalLazyRenderable::$payload);
        $this->assertStringContainsString('Open', $html);
    }
}
