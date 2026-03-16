<?php

declare(strict_types=1);

namespace Dcat\Admin\Form\Field;

/**
 * 阿里云图片字段
 *
 * 用于处理存储在阿里云 OSS 私有 Bucket 中的图片，
 * 自动生成带签名的临时访问 URL。
 */
class AliImage extends Image
{
    /**
     * 获取对象的签名 URL.
     */
    public function objectUrl($path): string
    {
        $url = $this->resolveAliSignUrl($path);
        if ($url !== null) {
            return $url;
        }

        // 如果没有定义 ali_sign_url 函数，返回原路径
        return $path;
    }

    protected function resolveAliSignUrl($path): ?string
    {
        if (! function_exists('ali_sign_url')) {
            return null;
        }

        return ali_sign_url($path);
    }
}
