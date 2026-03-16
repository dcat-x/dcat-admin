<?php

declare(strict_types=1);

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Support\Helper;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

class Downloadable extends AbstractDisplayer
{
    public function display($server = '', $disk = null)
    {
        $storage = $server ? null : Storage::disk($disk ?: config('admin.upload.disk'));

        return collect(Helper::array($this->value))->filter()->map(function ($value) use ($server, $storage) {
            $value = (string) $value;
            if (empty($value)) {
                return '';
            }

            if (filter_var($value, FILTER_VALIDATE_URL)) {
                $src = $value;
            } elseif ($server) {
                $src = rtrim($server, '/').'/'.ltrim($value, '/');
            } else {
                $src = $this->resolveStorageUrl($storage, $value);
            }

            $name = Helper::basename($value);

            return <<<HTML
<a href='$src' download='{$name}' target='_blank' class='text-muted'>
    <i class="feather icon-download"></i> {$name}
</a>
HTML;
        })->implode('<br>');
    }

    protected function resolveStorageUrl(Filesystem $storage, string $path): string
    {
        /** @var FilesystemAdapter $adapter */
        $adapter = $storage;

        return (string) $adapter->url($path);
    }
}
