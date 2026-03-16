<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\AliImage;
use Dcat\Admin\Tests\TestCase;

class AliImageTest extends TestCase
{
    public function test_object_url_uses_signed_url_when_available(): void
    {
        $field = new class('image', ['Image']) extends AliImage
        {
            protected function resolveAliSignUrl($path): ?string
            {
                return 'https://signed.example.com/'.$path;
            }
        };

        $this->assertSame('https://signed.example.com/uploads/photo.jpg', $field->objectUrl('uploads/photo.jpg'));
    }

    public function test_object_url_falls_back_to_original_path_when_signer_unavailable(): void
    {
        $field = new class('image', ['Image']) extends AliImage
        {
            protected function resolveAliSignUrl($path): ?string
            {
                return null;
            }
        };

        $this->assertSame('uploads/photo.jpg', $field->objectUrl('uploads/photo.jpg'));
    }

    public function test_object_url_preserves_empty_string_in_fallback_path(): void
    {
        $field = new class('image', ['Image']) extends AliImage
        {
            protected function resolveAliSignUrl($path): ?string
            {
                return null;
            }
        };

        $this->assertSame('', $field->objectUrl(''));
    }
}
