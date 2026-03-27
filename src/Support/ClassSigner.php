<?php

declare(strict_types=1);

namespace Dcat\Admin\Support;

use Dcat\Admin\Exception\AdminException;

class ClassSigner
{
    /**
     * Sign a class name with HMAC.
     */
    public static function sign(string $class): string
    {
        $signature = hash_hmac('sha256', $class, static::key());

        return $class.'|'.$signature;
    }

    /**
     * Verify a signed class string and return the class name.
     *
     * @throws AdminException
     */
    public static function verify(string $signed): string
    {
        $parts = explode('|', $signed, 2);

        if (count($parts) !== 2) {
            throw new AdminException('Invalid signed class format.');
        }

        [$class, $signature] = $parts;

        $expected = hash_hmac('sha256', $class, static::key());

        if (! hash_equals($expected, $signature)) {
            throw new AdminException('Class signature verification failed.');
        }

        return $class;
    }

    protected static function key(): string
    {
        return (string) config('app.key');
    }
}
