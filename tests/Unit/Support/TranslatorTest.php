<?php

namespace Dcat\Admin\Tests\Unit\Support;

use Dcat\Admin\Support\Translator;
use Dcat\Admin\Tests\TestCase;
use Mockery;

class TranslatorTest extends TestCase
{
    protected function tearDown(): void
    {
        // Reset static $method property
        $ref = new \ReflectionProperty(Translator::class, 'method');
        $ref->setAccessible(true);
        $ref->setValue(null, null);

        Mockery::close();
        parent::tearDown();
    }

    protected function createTranslator(?string $path = 'test-path'): Translator
    {
        $translator = new Translator;
        $translator->setPath($path);

        return $translator;
    }

    public function test_set_and_get_path(): void
    {
        $translator = new Translator;
        $translator->setPath('custom/path');
        $this->assertSame('custom/path', $translator->getPath());
    }

    public function test_set_path_to_null(): void
    {
        $translator = $this->createTranslator('initial');
        $translator->setPath(null);
        // After setting null, getPath will try Admin::context() which may fail,
        // so we just verify setPath accepted null
        $ref = new \ReflectionProperty($translator, 'path');
        $ref->setAccessible(true);
        $this->assertNull($ref->getValue($translator));
    }

    public function test_trans_returns_translation_when_key_exists(): void
    {
        // Register translation in the app translator
        $this->app['translator']->addLines([
            'test-path.fields.name' => 'Name Field',
        ], 'en');

        $translator = $this->createTranslator();
        $result = $translator->trans('test-path.fields.name');
        $this->assertSame('Name Field', $result);
    }

    public function test_trans_falls_back_to_global_prefix(): void
    {
        // Register a global translation
        $this->app['translator']->addLines([
            'global.fields.email' => 'Email Address',
        ], 'en');

        $translator = $this->createTranslator();
        // Try a key under non-existing path; should fallback to global
        $result = $translator->trans('users.fields.email');
        $this->assertSame('Email Address', $result);
    }

    public function test_trans_returns_last_segment_when_no_translation(): void
    {
        $translator = $this->createTranslator();
        $result = $translator->trans('some.path.unknown_field');
        $this->assertSame('unknown_field', $result);
    }

    public function test_trans_with_single_segment_key_returns_key(): void
    {
        $translator = $this->createTranslator();
        $result = $translator->trans('standalone');
        $this->assertSame('standalone', $result);
    }

    public function test_trans_field_delegates_correctly(): void
    {
        $this->app['translator']->addLines([
            'test-path.fields.username' => 'User Name',
        ], 'en');

        $translator = $this->createTranslator();
        $result = $translator->transField('username');
        $this->assertSame('User Name', $result);
    }

    public function test_trans_field_fallback_to_last_segment(): void
    {
        $translator = $this->createTranslator();
        $result = $translator->transField('nonexistent_field');
        $this->assertSame('nonexistent_field', $result);
    }

    public function test_trans_label_delegates_correctly(): void
    {
        $this->app['translator']->addLines([
            'test-path.labels.User' => 'User Label',
        ], 'en');

        $translator = $this->createTranslator();
        $result = $translator->transLabel('User');
        $this->assertSame('User Label', $result);
    }

    public function test_trans_label_fallback_to_last_segment(): void
    {
        $translator = $this->createTranslator();
        $result = $translator->transLabel('MissingLabel');
        $this->assertSame('MissingLabel', $result);
    }

    public function test_trans_with_replace_parameters(): void
    {
        $this->app['translator']->addLines([
            'test-path.messages.welcome' => 'Hello :name',
        ], 'en');

        $translator = $this->createTranslator();
        $result = $translator->trans('test-path.messages.welcome', ['name' => 'Admin']);
        $this->assertSame('Hello Admin', $result);
    }

    public function test_trans_global_key_is_not_re_prefixed(): void
    {
        $this->app['translator']->addLines([
            'global.labels.title' => 'Global Title',
        ], 'en');

        $translator = $this->createTranslator();
        // When key already starts with 'global.', it should not re-prefix
        $result = $translator->trans('global.labels.title');
        $this->assertSame('Global Title', $result);
    }

    public function test_trans_global_fallback_when_global_also_missing(): void
    {
        $translator = $this->createTranslator();
        // Neither original key nor global key exist
        $result = $translator->trans('some-path.fields.missing');
        $this->assertSame('missing', $result);
    }
}
