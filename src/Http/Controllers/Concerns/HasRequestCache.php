<?php

namespace Dcat\Admin\Http\Controllers\Concerns;

trait HasRequestCache
{
    /**
     * @var int|null
     */
    protected static $requestHash;

    /**
     * @var array<string, mixed>
     */
    protected static $requestCache = [];

    protected function refreshRequestCacheIfNeeded(): void
    {
        $requestHash = spl_object_id(request());

        if (static::$requestHash === $requestHash) {
            return;
        }

        static::$requestHash = $requestHash;
        static::$requestCache = [];
    }

    protected function rememberRequestCache(string $key, callable $resolver)
    {
        $this->refreshRequestCacheIfNeeded();

        if (array_key_exists($key, static::$requestCache)) {
            return static::$requestCache[$key];
        }

        return static::$requestCache[$key] = $resolver();
    }
}
