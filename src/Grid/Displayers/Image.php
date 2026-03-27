<?php

declare(strict_types=1);

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Support\Helper;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

class Image extends AbstractDisplayer
{
    public function display($server = '', $width = 200, $height = 200)
    {
        if ($this->value instanceof Arrayable) {
            $this->value = $this->value->toArray();
        }
        $this->value = Helper::array($this->value);
        $storage = $server ? null : Storage::disk(config('admin.upload.disk'));

        return collect((array) $this->value)->filter()->map(function ($path) use ($server, $width, $height, $storage) {
            $path = (string) $path;
            if (filter_var($path, FILTER_VALIDATE_URL) || str_starts_with($path, 'data:image')) {
                $src = $path;
            } elseif ($server) {
                $src = rtrim((string) $server, '/').'/'.ltrim($path, '/');
            } else {
                $src = $this->resolveStorageUrl($storage, $path);
            }

            return "<img data-action='preview-img' src='$src' style='max-width:{$width}px;max-height:{$height}px;cursor:pointer' class='img img-thumbnail' />";
        })->implode('&nbsp;');
    }

    protected function resolveStorageUrl(Filesystem $storage, string $path): string
    {
        /** @var FilesystemAdapter $adapter */
        $adapter = $storage;

        return (string) $adapter->url($path);
    }
}
