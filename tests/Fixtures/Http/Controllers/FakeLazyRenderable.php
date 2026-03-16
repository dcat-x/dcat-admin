<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Fixtures\Http\Controllers;

use Dcat\Admin\Contracts\LazyRenderable;

class FakeLazyRenderable implements LazyRenderable
{
    public static bool $authorized = true;

    public static bool $requireAssetsCalled = false;

    public static array $payload = [];

    public static string $failedAuthorizationResponse = 'forbidden';

    public static function reset(): void
    {
        static::$authorized = true;
        static::$requireAssetsCalled = false;
        static::$payload = [];
        static::$failedAuthorizationResponse = 'forbidden';
    }

    public function getUrl()
    {
        return '/fake/lazy';
    }

    public function render()
    {
        return '<div id="fake-lazy-renderable">ok</div>';
    }

    public function payload(array $payload)
    {
        static::$payload = $payload;

        return $this;
    }

    public function requireAssets(): void
    {
        static::$requireAssetsCalled = true;
    }

    public function passesAuthorization(): bool
    {
        return static::$authorized;
    }

    public function failedAuthorization()
    {
        return static::$failedAuthorizationResponse;
    }
}
