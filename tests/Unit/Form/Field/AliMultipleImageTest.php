<?php

namespace Dcat\Admin\Tests\Unit\Form\Field;

use Dcat\Admin\Form\Field\AliMultipleImage;
use Dcat\Admin\Tests\TestCase;

class AliMultipleImageTest extends TestCase
{
    public function test_object_url_uses_signed_url_when_available(): void
    {
        $field = new class('photos', ['Photos']) extends AliMultipleImage
        {
            protected function resolveAliSignUrl($path): ?string
            {
                return 'https://signed.example.com/'.$path;
            }
        };

        $this->assertSame('https://signed.example.com/uploads/photos/1.jpg', $field->objectUrl('uploads/photos/1.jpg'));
    }

    public function test_object_url_falls_back_to_original_path_when_signer_unavailable(): void
    {
        $field = new class('photos', ['Photos']) extends AliMultipleImage
        {
            protected function resolveAliSignUrl($path): ?string
            {
                return null;
            }
        };

        $this->assertSame('uploads/photos/1.jpg', $field->objectUrl('uploads/photos/1.jpg'));
    }

    public function test_object_url_preserves_empty_string_in_fallback_path(): void
    {
        $field = new class('photos', ['Photos']) extends AliMultipleImage
        {
            protected function resolveAliSignUrl($path): ?string
            {
                return null;
            }
        };

        $this->assertSame('', $field->objectUrl(''));
    }
}
