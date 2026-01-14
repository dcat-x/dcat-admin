<?php

namespace Dcat\Admin\Form\Field;

use Illuminate\Support\Str;

/**
 * OSS 直传上传组件
 *
 * 支持前端直连 OSS 上传大文件，使用 STS 临时授权。
 * 支持分片上传、断点续传。
 */
class OssDirectUpload extends File
{
    /**
     * 视图模板.
     */
    protected $view = 'admin::form.oss-direct-upload';

    /**
     * 最大文件大小（MB）.
     */
    protected int $maxSizeMb = 500;

    /**
     * 分片大小（MB）.
     */
    protected int $chunkSizeMb = 10;

    /**
     * 文件类型（file 或 image）.
     */
    protected string $uploadType = 'file';

    /**
     * 自定义上传目录.
     */
    protected ?string $uploadDirectory = null;

    /**
     * 允许的文件扩展名.
     */
    protected string $acceptExtensions = '*';

    /**
     * 允许的 MIME 类型.
     */
    protected ?string $acceptMimeTypes = null;

    /**
     * STS Token 获取地址.
     */
    protected ?string $stsTokenUrl = null;

    /**
     * 设置最大文件大小（MB）.
     */
    public function maxSize(int $mb): static
    {
        $this->maxSizeMb = $mb;

        return $this;
    }

    /**
     * 设置分片大小（MB）.
     */
    public function chunkSize(int $mb): static
    {
        $this->chunkSizeMb = $mb;

        return $this;
    }

    /**
     * 设置文件类型限制.
     */
    public function accept(string $extensions, ?string $mimeTypes = null): static
    {
        $this->acceptExtensions = $extensions;
        $this->acceptMimeTypes = $mimeTypes;

        return $this;
    }

    /**
     * 设置上传类型.
     */
    public function uploadType(string $type): static
    {
        $this->uploadType = $type;

        return $this;
    }

    /**
     * 设置自定义上传目录.
     */
    public function directory(string $directory): static
    {
        $this->uploadDirectory = $directory;

        return $this;
    }

    /**
     * 设置 STS Token 获取地址.
     */
    public function stsTokenUrl(string $url): static
    {
        $this->stsTokenUrl = $url;

        return $this;
    }

    /**
     * 渲染组件.
     */
    public function render()
    {
        $id = $this->id ?? Str::random(8);

        $this->addVariables([
            'id' => $id,
            'column' => $this->column,
            'name' => $this->getElementName(),
            'value' => $this->value ?? '',
            'maxSize' => $this->maxSizeMb,
            'chunkSize' => $this->chunkSizeMb,
            'accept' => $this->acceptExtensions,
            'acceptMimeTypes' => $this->acceptMimeTypes,
            'uploadType' => $this->uploadType,
            'uploadDirectory' => $this->uploadDirectory,
            'stsTokenUrl' => $this->stsTokenUrl ?? admin_url('oss/sts-token'),
            'help' => $this->help,
        ]);

        return parent::render();
    }
}
