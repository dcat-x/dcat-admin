<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\Image;
use Dcat\Admin\Tests\TestCase;
use Mockery;

// 注册 Intervention\Image\ImageManagerStatic 存根类，
// 使 ImageField::__call() 的 class_exists 检查通过。
if (! class_exists(\Intervention\Image\ImageManagerStatic::class)) {
    class_alias(ImageManagerStaticStub::class, \Intervention\Image\ImageManagerStatic::class);
}

/**
 * ImageManagerStatic 存根，仅用于满足 class_exists 检查。
 */
class ImageManagerStaticStub {}

/**
 * 测试 Image 类及 ImageField trait 的核心功能。
 */
class ImageTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * 创建 Image 字段实例，并注入 mock storage。
     */
    protected function createImageField(string $column = 'avatar', string $label = 'Avatar'): Image
    {
        $field = new Image($column, [$label]);

        $storage = Mockery::mock(\Illuminate\Contracts\Filesystem\Filesystem::class);
        $storage->shouldReceive('exists')->andReturn(false)->byDefault();
        $storage->shouldReceive('delete')->andReturn(true)->byDefault();
        $storage->shouldReceive('url')->andReturn('')->byDefault();

        $reflection = new \ReflectionProperty($field, 'storage');
        $reflection->setAccessible(true);
        $reflection->setValue($field, $storage);

        return $field;
    }

    /**
     * 通过反射获取 protected/private 属性值。
     */
    protected function getProtectedProperty(object $object, string $property): mixed
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    /**
     * 通过反射设置 protected/private 属性值。
     */
    protected function setProtectedProperty(object $object, string $property, mixed $value): void
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);
        $reflection->setValue($object, $value);
    }

    // -------------------------------------------------------
    // Image 类构造函数 & setupImage
    // -------------------------------------------------------

    public function test_constructor_calls_setup_image(): void
    {
        $field = $this->createImageField();

        $options = $this->getProtectedProperty($field, 'options');

        $this->assertArrayHasKey('accept', $options);
        $this->assertSame('image/*', $options['accept']['mimeTypes']);
        $this->assertTrue($options['isImage']);
    }

    public function test_setup_image_preserves_existing_accept_keys(): void
    {
        $field = $this->createImageField();

        // setupImage 已在构造时运行；验证 accept 数组包含 mimeTypes
        $options = $this->getProtectedProperty($field, 'options');

        $this->assertIsArray($options['accept']);
        $this->assertArrayHasKey('mimeTypes', $options['accept']);
        $this->assertSame('image/*', $options['accept']['mimeTypes']);
    }

    public function test_setup_image_sets_is_image_to_true(): void
    {
        $field = $this->createImageField();

        $options = $this->getProtectedProperty($field, 'options');

        $this->assertTrue($options['isImage']);
    }

    // -------------------------------------------------------
    // 默认 rules
    // -------------------------------------------------------

    public function test_default_rules_contain_nullable_and_image(): void
    {
        $field = $this->createImageField();

        $rules = $this->getProtectedProperty($field, 'rules');

        $this->assertIsArray($rules);
        $this->assertContains('nullable', $rules);
        $this->assertContains('image', $rules);
    }

    // -------------------------------------------------------
    // dimensions()
    // -------------------------------------------------------

    public function test_dimensions_adds_validation_rule(): void
    {
        $field = $this->createImageField();

        $result = $field->dimensions(['width' => 100, 'height' => 200]);

        // 返回 $this 以支持链式调用
        $this->assertSame($field, $result);

        $rules = $this->getProtectedProperty($field, 'rules');

        // 确认 dimensions 规则被添加
        $hasDimensionsRule = false;
        foreach ($rules as $rule) {
            if (is_string($rule) && str_contains($rule, 'dimensions:')) {
                $hasDimensionsRule = true;
                $this->assertStringContainsString('width=100', $rule);
                $this->assertStringContainsString('height=200', $rule);
            }
        }
        $this->assertTrue($hasDimensionsRule, 'Expected a dimensions validation rule to be present.');
    }

    public function test_dimensions_merges_into_options(): void
    {
        $field = $this->createImageField();

        $field->dimensions(['min_width' => 50, 'max_height' => 300]);

        $options = $this->getProtectedProperty($field, 'options');

        $this->assertArrayHasKey('dimensions', $options);
        $this->assertSame(50, $options['dimensions']['min_width']);
        $this->assertSame(300, $options['dimensions']['max_height']);
    }

    public function test_dimensions_with_empty_array_returns_this(): void
    {
        $field = $this->createImageField();

        $result = $field->dimensions([]);

        $this->assertSame($field, $result);

        // 不应添加 dimensions 规则
        $rules = $this->getProtectedProperty($field, 'rules');
        foreach ($rules as $rule) {
            if (is_string($rule)) {
                $this->assertStringNotContainsString('dimensions:', $rule);
            }
        }
    }

    // -------------------------------------------------------
    // ratio()
    // -------------------------------------------------------

    public function test_ratio_sets_dimensions_with_ratio(): void
    {
        $field = $this->createImageField();

        $result = $field->ratio(1.5);

        $this->assertSame($field, $result);

        $rules = $this->getProtectedProperty($field, 'rules');

        $hasDimensionsRule = false;
        foreach ($rules as $rule) {
            if (is_string($rule) && str_contains($rule, 'dimensions:')) {
                $hasDimensionsRule = true;
                $this->assertStringContainsString('ratio=1.5', $rule);
            }
        }
        $this->assertTrue($hasDimensionsRule, 'Expected a dimensions:ratio validation rule.');
    }

    public function test_ratio_zero_returns_this_without_rule(): void
    {
        $field = $this->createImageField();

        $result = $field->ratio(0);

        $this->assertSame($field, $result);

        // 不应该有 dimensions 规则
        $rules = $this->getProtectedProperty($field, 'rules');
        foreach ($rules as $rule) {
            if (is_string($rule)) {
                $this->assertStringNotContainsString('dimensions:', $rule);
            }
        }
    }

    public function test_ratio_negative_returns_this_without_rule(): void
    {
        $field = $this->createImageField();

        $result = $field->ratio(-2.5);

        $this->assertSame($field, $result);

        $rules = $this->getProtectedProperty($field, 'rules');
        foreach ($rules as $rule) {
            if (is_string($rule)) {
                $this->assertStringNotContainsString('dimensions:', $rule);
            }
        }
    }

    // -------------------------------------------------------
    // ImageField trait — defaultDirectory()
    // -------------------------------------------------------

    public function test_default_directory_returns_config_value(): void
    {
        $this->app['config']->set('admin.upload.directory.image', 'images');

        $field = $this->createImageField();

        $this->assertSame('images', $field->defaultDirectory());
    }

    public function test_default_directory_returns_null_when_config_not_set(): void
    {
        $this->app['config']->set('admin.upload.directory.image', null);

        $field = $this->createImageField();

        $this->assertNull($field->defaultDirectory());
    }

    // -------------------------------------------------------
    // ImageField trait — thumbnail()
    // -------------------------------------------------------

    public function test_thumbnail_with_three_arguments(): void
    {
        $field = $this->createImageField();

        $result = $field->thumbnail('small', 100, 100);

        $this->assertSame($field, $result);

        $thumbnails = $this->getProtectedProperty($field, 'thumbnails');

        $this->assertArrayHasKey('small', $thumbnails);
        $this->assertSame([100, 100], $thumbnails['small']);
    }

    public function test_thumbnail_with_array_batch(): void
    {
        $field = $this->createImageField();

        $result = $field->thumbnail([
            'small' => [150, 150],
            'medium' => [300, 300],
            'large' => [600, 400],
        ]);

        $this->assertSame($field, $result);

        $thumbnails = $this->getProtectedProperty($field, 'thumbnails');

        $this->assertCount(3, $thumbnails);
        $this->assertSame([150, 150], $thumbnails['small']);
        $this->assertSame([300, 300], $thumbnails['medium']);
        $this->assertSame([600, 400], $thumbnails['large']);
    }

    public function test_thumbnail_array_skips_entries_with_less_than_two_elements(): void
    {
        $field = $this->createImageField();

        $field->thumbnail([
            'valid' => [100, 200],
            'invalid' => [100],      // count < 2, 应被跳过
        ]);

        $thumbnails = $this->getProtectedProperty($field, 'thumbnails');

        $this->assertArrayHasKey('valid', $thumbnails);
        $this->assertArrayNotHasKey('invalid', $thumbnails);
    }

    public function test_thumbnail_can_be_called_multiple_times(): void
    {
        $field = $this->createImageField();

        $field->thumbnail('small', 100, 100);
        $field->thumbnail('large', 800, 600);

        $thumbnails = $this->getProtectedProperty($field, 'thumbnails');

        $this->assertCount(2, $thumbnails);
        $this->assertSame([100, 100], $thumbnails['small']);
        $this->assertSame([800, 600], $thumbnails['large']);
    }

    // -------------------------------------------------------
    // ImageField trait — __call() (intervention 调用记录)
    // -------------------------------------------------------

    public function test_call_records_intervention_method(): void
    {
        $field = $this->createImageField();

        $result = $field->resize(200, 200);

        $this->assertSame($field, $result);

        $calls = $this->getProtectedProperty($field, 'interventionCalls');

        $this->assertCount(1, $calls);
        $this->assertSame('resize', $calls[0]['method']);
        $this->assertSame([200, 200], $calls[0]['arguments']);
    }

    public function test_call_records_multiple_intervention_methods(): void
    {
        $field = $this->createImageField();

        $field->resize(300, null)->crop(100, 100)->blur(5);

        $calls = $this->getProtectedProperty($field, 'interventionCalls');

        $this->assertCount(3, $calls);
        $this->assertSame('resize', $calls[0]['method']);
        $this->assertSame('crop', $calls[1]['method']);
        $this->assertSame('blur', $calls[2]['method']);
    }

    public function test_call_resolves_intervention_alias(): void
    {
        $field = $this->createImageField();

        // 'filling' 是 'fill' 的别名
        $field->filling('#ffffff');

        $calls = $this->getProtectedProperty($field, 'interventionCalls');

        $this->assertCount(1, $calls);
        $this->assertSame('fill', $calls[0]['method']);
        $this->assertSame(['#ffffff'], $calls[0]['arguments']);
    }

    // -------------------------------------------------------
    // ImageField trait — destroyThumbnail()
    // -------------------------------------------------------

    public function test_destroy_thumbnail_deletes_existing_thumbnail_files(): void
    {
        $field = $this->createImageField();

        $field->thumbnail('small', 100, 100);
        $field->thumbnail('large', 400, 400);

        $this->setProtectedProperty($field, 'original', 'uploads/photo.jpg');

        $storage = Mockery::mock(\Illuminate\Contracts\Filesystem\Filesystem::class);
        $storage->shouldReceive('exists')
            ->with('uploads/photo-small.jpg')
            ->once()
            ->andReturn(true);
        $storage->shouldReceive('delete')
            ->with('uploads/photo-small.jpg')
            ->once()
            ->andReturn(true);
        $storage->shouldReceive('exists')
            ->with('uploads/photo-large.jpg')
            ->once()
            ->andReturn(true);
        $storage->shouldReceive('delete')
            ->with('uploads/photo-large.jpg')
            ->once()
            ->andReturn(true);

        $this->setProtectedProperty($field, 'storage', $storage);

        $field->destroyThumbnail();

        $this->addToAssertionCount(1);
    }

    public function test_destroy_thumbnail_skips_non_existing_files(): void
    {
        $field = $this->createImageField();

        $field->thumbnail('small', 100, 100);

        $this->setProtectedProperty($field, 'original', 'uploads/photo.png');

        $storage = Mockery::mock(\Illuminate\Contracts\Filesystem\Filesystem::class);
        $storage->shouldReceive('exists')
            ->with('uploads/photo-small.png')
            ->once()
            ->andReturn(false);
        $storage->shouldNotReceive('delete');

        $this->setProtectedProperty($field, 'storage', $storage);

        $field->destroyThumbnail();

        $this->addToAssertionCount(1);
    }

    public function test_destroy_thumbnail_with_explicit_file_argument(): void
    {
        $field = $this->createImageField();

        $field->thumbnail('thumb', 200, 200);

        $storage = Mockery::mock(\Illuminate\Contracts\Filesystem\Filesystem::class);
        $storage->shouldReceive('exists')
            ->with('images/banner-thumb.jpeg')
            ->once()
            ->andReturn(true);
        $storage->shouldReceive('delete')
            ->with('images/banner-thumb.jpeg')
            ->once()
            ->andReturn(true);

        $this->setProtectedProperty($field, 'storage', $storage);

        $field->destroyThumbnail('images/banner.jpeg');

        $this->addToAssertionCount(1);
    }

    public function test_destroy_thumbnail_handles_array_of_files(): void
    {
        $field = $this->createImageField();

        $field->thumbnail('sm', 100, 100);

        $storage = Mockery::mock(\Illuminate\Contracts\Filesystem\Filesystem::class);
        $storage->shouldReceive('exists')
            ->with('a-sm.jpg')
            ->once()
            ->andReturn(true);
        $storage->shouldReceive('delete')
            ->with('a-sm.jpg')
            ->once()
            ->andReturn(true);
        $storage->shouldReceive('exists')
            ->with('b-sm.png')
            ->once()
            ->andReturn(true);
        $storage->shouldReceive('delete')
            ->with('b-sm.png')
            ->once()
            ->andReturn(true);

        $this->setProtectedProperty($field, 'storage', $storage);

        $field->destroyThumbnail(['a.jpg', 'b.png']);

        $this->addToAssertionCount(1);
    }

    public function test_destroy_thumbnail_respects_retainable_flag(): void
    {
        $field = $this->createImageField();

        $field->thumbnail('small', 100, 100);

        $this->setProtectedProperty($field, 'retainable', true);
        $this->setProtectedProperty($field, 'original', 'uploads/photo.jpg');

        $storage = Mockery::mock(\Illuminate\Contracts\Filesystem\Filesystem::class);
        $storage->shouldNotReceive('exists');
        $storage->shouldNotReceive('delete');

        $this->setProtectedProperty($field, 'storage', $storage);

        // retainable = true 且 force = false 时不应删除
        $field->destroyThumbnail();

        $this->addToAssertionCount(1);
    }

    public function test_destroy_thumbnail_force_overrides_retainable(): void
    {
        $field = $this->createImageField();

        $field->thumbnail('small', 100, 100);

        $this->setProtectedProperty($field, 'retainable', true);
        $this->setProtectedProperty($field, 'original', 'uploads/photo.jpg');

        $storage = Mockery::mock(\Illuminate\Contracts\Filesystem\Filesystem::class);
        $storage->shouldReceive('exists')
            ->with('uploads/photo-small.jpg')
            ->once()
            ->andReturn(true);
        $storage->shouldReceive('delete')
            ->with('uploads/photo-small.jpg')
            ->once()
            ->andReturn(true);

        $this->setProtectedProperty($field, 'storage', $storage);

        // force = true 时即使 retainable 也应删除
        $field->destroyThumbnail(null, true);

        $this->addToAssertionCount(1);
    }

    public function test_destroy_thumbnail_returns_early_when_no_file(): void
    {
        $field = $this->createImageField();

        $field->thumbnail('small', 100, 100);

        $this->setProtectedProperty($field, 'original', null);

        $storage = Mockery::mock(\Illuminate\Contracts\Filesystem\Filesystem::class);
        $storage->shouldNotReceive('exists');
        $storage->shouldNotReceive('delete');

        $this->setProtectedProperty($field, 'storage', $storage);

        // original 为 null 且未传文件参数，应直接返回
        $field->destroyThumbnail();

        $this->addToAssertionCount(1);
    }

    // -------------------------------------------------------
    // callInterventionMethods — 无调用时直接返回 target
    // -------------------------------------------------------

    public function test_call_intervention_methods_returns_target_when_no_calls(): void
    {
        $field = $this->createImageField();

        // interventionCalls 默认为空数组，调用 callInterventionMethods 应直接返回 target
        $result = $field->callInterventionMethods('/tmp/test.jpg', 'image/jpeg');

        $this->assertSame('/tmp/test.jpg', $result);
    }

    // -------------------------------------------------------
    // $view 属性
    // -------------------------------------------------------

    public function test_view_is_admin_form_file(): void
    {
        $field = $this->createImageField();

        $view = $this->getProtectedProperty($field, 'view');

        $this->assertSame('admin::form.file', $view);
    }
}
