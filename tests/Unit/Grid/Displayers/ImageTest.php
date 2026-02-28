<?php

namespace Dcat\Admin\Tests\Unit\Grid\Displayers;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Column;
use Dcat\Admin\Grid\Displayers\Image;
use Dcat\Admin\Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use Mockery;

class ImageTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function makeDisplayer($value): Image
    {
        $grid = Mockery::mock(Grid::class);
        $grid->shouldReceive('getName')->andReturn('test');

        $column = Mockery::mock(Column::class);
        $column->shouldReceive('getName')->andReturn('avatar');

        $row = ['id' => 1, 'avatar' => $value];

        return new Image($value, $grid, $column, $row);
    }

    public function test_display_full_url(): void
    {
        $displayer = $this->makeDisplayer('https://example.com/image.jpg');
        $result = $displayer->display();

        $this->assertStringContainsString("src='https://example.com/image.jpg'", $result);
        $this->assertStringContainsString('<img', $result);
        $this->assertStringContainsString('img-thumbnail', $result);
    }

    public function test_display_with_custom_server(): void
    {
        $displayer = $this->makeDisplayer('uploads/avatar.png');
        $result = $displayer->display('https://cdn.example.com');

        $this->assertStringContainsString("src='https://cdn.example.com/uploads/avatar.png'", $result);
    }

    public function test_display_with_custom_width_and_height(): void
    {
        $displayer = $this->makeDisplayer('https://example.com/image.jpg');
        $result = $displayer->display('', 100, 100);

        $this->assertStringContainsString('max-width:100px', $result);
        $this->assertStringContainsString('max-height:100px', $result);
    }

    public function test_display_default_width_and_height(): void
    {
        $displayer = $this->makeDisplayer('https://example.com/image.jpg');
        $result = $displayer->display();

        $this->assertStringContainsString('max-width:200px', $result);
        $this->assertStringContainsString('max-height:200px', $result);
    }

    public function test_display_data_image(): void
    {
        // data:image URLs contain commas, which Helper::array splits on.
        // When passed as array element, the url() check detects it properly.
        $dataImage = 'data:image/png;base64,iVBORw0KGgo=';
        $displayer = $this->makeDisplayer([$dataImage]);
        $result = $displayer->display();

        $this->assertStringContainsString($dataImage, $result);
    }

    public function test_display_array_of_images(): void
    {
        $displayer = $this->makeDisplayer([
            'https://example.com/img1.jpg',
            'https://example.com/img2.jpg',
        ]);
        $result = $displayer->display();

        $this->assertStringContainsString('img1.jpg', $result);
        $this->assertStringContainsString('img2.jpg', $result);
        // Multiple images separated by nbsp
        $this->assertStringContainsString('&nbsp;', $result);
    }

    public function test_display_with_storage_path(): void
    {
        Storage::fake(config('admin.upload.disk', 'public'));

        $displayer = $this->makeDisplayer('photos/test.jpg');
        $result = $displayer->display();

        $this->assertStringContainsString('<img', $result);
        $this->assertStringContainsString('photos/test.jpg', $result);
    }

    public function test_display_with_server_trims_slashes(): void
    {
        $displayer = $this->makeDisplayer('/images/photo.jpg');
        $result = $displayer->display('https://cdn.example.com/');

        $this->assertStringContainsString("src='https://cdn.example.com/images/photo.jpg'", $result);
    }

    public function test_display_empty_value_returns_empty(): void
    {
        $displayer = $this->makeDisplayer('');
        $result = $displayer->display();

        $this->assertEmpty($result);
    }

    public function test_display_has_preview_action(): void
    {
        $displayer = $this->makeDisplayer('https://example.com/image.jpg');
        $result = $displayer->display();

        $this->assertStringContainsString("data-action='preview-img'", $result);
        $this->assertStringContainsString('cursor:pointer', $result);
    }
}
