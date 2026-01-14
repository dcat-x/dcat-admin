<?php

namespace Dcat\Admin\Form\Field;

/**
 * 阿里云多图片字段
 *
 * 用于处理存储在阿里云 OSS 私有 Bucket 中的多张图片，
 * 自动生成带签名的临时访问 URL。
 */
class AliMultipleImage extends MultipleImage
{
    /**
     * 获取对象的签名 URL.
     */
    public function objectUrl($path): string
    {
        if (function_exists('ali_sign_url')) {
            return ali_sign_url($path);
        }

        // 如果没有定义 ali_sign_url 函数，返回原路径
        return $path;
    }
}
