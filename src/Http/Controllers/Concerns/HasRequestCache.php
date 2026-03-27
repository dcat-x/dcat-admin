<?php

declare(strict_types=1);

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

    /**
     * 显式清理请求级缓存，用于 Octane 等长生命周期进程。
     */
    public static function resetRequestCache(): void
    {
        static::$requestHash = null;
        static::$requestCache = [];
    }
}
