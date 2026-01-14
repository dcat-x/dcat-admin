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
     *
     * @var string
     */
    protected $view = 'admin::form.oss-direct-upload';

    /**
     * 最大文件大小（MB）.
     *
     * @var int
     */
    protected int $maxSizeMb = 500;

    /**
     * 分片大小（MB）.
     *
     * @var int
     */
    protected int $chunkSizeMb = 10;

    /**
     * 文件类型（file 或 image）.
     *
     * @var string
     */
    protected string $uploadType = 'file';

    /**
     * 自定义上传目录.
     *
     * @var string|null
     */
    protected ?string $uploadDirectory = null;

    /**
     * 允许的文件扩展名.
     *
     * @var string
     */
    protected string $acceptExtensions = '*';

    /**
     * 允许的 MIME 类型.
     *
     * @var string|null
     */
    protected ?string $acceptMimeTypes = null;

    /**
     * STS Token 获取地址.
     *
     * @var string|null
     */
    protected ?string $stsTokenUrl = null;

    /**
     * 设置最大文件大小（MB）.
     *
     * @param  int  $mb
     * @return $this
     */
    public function maxSize(int $mb): static
    {
        $this->maxSizeMb = $mb;

        return $this;
    }

    /**
     * 设置分片大小（MB）.
     *
     * @param  int  $mb
     * @return $this
     */
    public function chunkSize(int $mb): static
    {
        $this->chunkSizeMb = $mb;

        return $this;
    }

    /**
     * 设置文件类型限制.
     *
     * @param  string  $extensions  允许的扩展名，如 'jpg,png,pdf'
     * @param  string|null  $mimeTypes  允许的 MIME 类型
     * @return $this
     */
    public function accept(string $extensions, ?string $mimeTypes = null): static
    {
        $this->acceptExtensions = $extensions;
        $this->acceptMimeTypes = $mimeTypes;

        return $this;
    }

    /**
     * 设置上传类型.
     *
     * @param  string  $type  file 或 image
     * @return $this
     */
    public function uploadType(string $type): static
    {
        $this->uploadType = $type;

        return $this;
    }

    /**
     * 设置自定义上传目录.
     *
     * @param  string  $directory  自定义目录，例如: 'apk/android', 'documents/contracts'
     * @return $this
     */
    public function directory(string $directory): static
    {
        $this->uploadDirectory = $directory;

        return $this;
    }

    /**
     * 设置 STS Token 获取地址.
     *
     * @param  string  $url
     * @return $this
     */
    public function stsTokenUrl(string $url): static
    {
        $this->stsTokenUrl = $url;

        return $this;
    }

    /**
     * 渲染组件.
     *
     * @return mixed
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
