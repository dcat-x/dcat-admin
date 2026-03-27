<?php

declare(strict_types=1);

namespace Dcat\Admin\Support;

use Dcat\Admin\Exception\AdminException;
use Illuminate\Support\Facades\Log;

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
     * If the signed string has no signature (legacy format from cached pages),
     * the class name is returned with a deprecation warning logged.
     *
     * @throws AdminException
     */
    public static function verify(string $signed): string
    {
        $parts = explode('|', $signed, 2);

        if (count($parts) !== 2) {
            // Legacy format: unsigned class name from cached page
            Log::warning('admin.class_signer.unsigned', [
                'class' => $signed,
                'hint' => 'Client submitted unsigned class name. This usually means a cached page was loaded before the signing upgrade. The user should refresh the page.',
            ]);

            return $signed;
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
