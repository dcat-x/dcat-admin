<?php

namespace Dcat\Admin\Tests\Unit\Traits;

use Dcat\Admin\Tests\TestCase;
use Dcat\Admin\Traits\Resizable;

class ResizableTestModel
{
    use Resizable;

    public $attributes = [];

    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }
}

class ResizableTest extends TestCase
{
    public function test_thumbnail_generates_correct_path(): void
    {
        $model = new ResizableTestModel(['image' => 'photos/avatar.jpg']);

        $result = $model->thumbnail('small');

        $this->assertSame('photos/avatar-small.jpg', $result);
    }

    public function test_thumbnail_with_png_extension(): void
    {
        $model = new ResizableTestModel(['image' => 'uploads/photo.png']);

        $result = $model->thumbnail('medium');

        $this->assertSame('uploads/photo-medium.png', $result);
    }

    public function test_thumbnail_with_custom_attribute(): void
    {
        $model = new ResizableTestModel(['avatar' => 'users/profile.jpeg']);

        $result = $model->thumbnail('thumb', 'avatar');

        $this->assertSame('users/profile-thumb.jpeg', $result);
    }

    public function test_thumbnail_returns_empty_when_attribute_not_set(): void
    {
        $model = new ResizableTestModel([]);

        $result = $model->thumbnail('small');

        $this->assertSame('', $result);
    }

    public function test_get_thumbnail_path(): void
    {
        $model = new ResizableTestModel;

        $result = $model->getThumbnailPath('images/test.jpg', 'large');

        $this->assertSame('images/test-large.jpg', $result);
    }

    public function test_get_thumbnail_path_with_multiple_dots(): void
    {
        $model = new ResizableTestModel;

        $result = $model->getThumbnailPath('images/my.file.name.jpg', 'thumb');

        $this->assertSame('images/my.file.name-thumb.jpg', $result);
    }

    public function test_thumbnail_with_different_types(): void
    {
        $model = new ResizableTestModel(['image' => 'photo.jpg']);

        $this->assertSame('photo-small.jpg', $model->thumbnail('small'));
        $this->assertSame('photo-medium.jpg', $model->thumbnail('medium'));
        $this->assertSame('photo-large.jpg', $model->thumbnail('large'));
    }

    public function test_get_thumbnail_path_preserves_directory(): void
    {
        $model = new ResizableTestModel;

        $result = $model->getThumbnailPath('path/to/deep/image.gif', 'crop');

        $this->assertSame('path/to/deep/image-crop.gif', $result);
    }
}
