<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Http\Controllers\Concerns;

use Dcat\Admin\Http\Controllers\Concerns\HasRequestCache;
use Dcat\Admin\Tests\TestCase;

class HasRequestCacheTest extends TestCase
{
    public function test_reset_request_cache_clears_state(): void
    {
        $instance = new class
        {
            use HasRequestCache;

            public function warm(): void
            {
                $this->rememberRequestCache('key', fn () => 'value');
            }

            public function has(): bool
            {
                return array_key_exists('key', self::$requestCache);
            }
        };

        $instance->warm();
        $this->assertTrue($instance->has());

        $instance::resetRequestCache();
        $this->assertFalse($instance->has());
    }

    public function test_reset_request_cache_clears_hash(): void
    {
        $instance = new class
        {
            use HasRequestCache;

            public function getHash(): ?int
            {
                return self::$requestHash;
            }

            public function warm(): void
            {
                $this->rememberRequestCache('k', fn () => 'v');
            }
        };

        $instance->warm();
        $this->assertNotNull($instance->getHash());

        $instance::resetRequestCache();
        $this->assertNull($instance->getHash());
    }
}
