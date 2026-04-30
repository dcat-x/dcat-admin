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
     * 默认拒绝未签名输入。升级期间为兼容老缓存页面，
     * 可通过 config('admin.allow_unsigned_dispatch') = true 临时放行；
     * 放行时仅记录 warning，且建议升级稳定后立即关闭，避免攻击者
     * 绕过签名直接派发任意已存在的类。
     *
     * @throws AdminException
     */
    public static function verify(string $signed): string
    {
        $parts = explode('|', $signed, 2);

        if (count($parts) !== 2) {
            if (! (bool) config('admin.allow_unsigned_dispatch', false)) {
                throw new AdminException('Class signature missing.');
            }

            Log::warning('admin.class_signer.unsigned', [
                'class' => $signed,
                'hint' => 'Client submitted unsigned class name. This usually means a cached page was loaded before the signing upgrade. The user should refresh the page. Disable admin.allow_unsigned_dispatch once all users have refreshed.',
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
