<?php

declare(strict_types=1);

namespace Dcat\Admin\Http\Controllers\Concerns;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\UploadedFile;

trait UploadsFiles
{
    protected function validateUploadFile(Request $request, string $fieldName): UploadedFile
    {
        $request->validate([
            $fieldName => 'required|file',
        ]);

        return $request->file($fieldName);
    }

    protected function generateNewName(UploadedFile $file): string
    {
        return Str::random(32).'.'.$file->getClientOriginalExtension();
    }

    protected function disk(): Filesystem
    {
        $disk = request()->get('disk') ?: config('admin.upload.disk');

        return Storage::disk($disk);
    }

    protected function putFileAs(Filesystem $disk, string $dir, UploadedFile $file, string $name): void
    {
        /** @var \Illuminate\Http\UploadedFile $uploadedFile */
        $uploadedFile = $file;
        $disk->putFileAs($dir, $uploadedFile, $name);
    }

    protected function diskUrl(Filesystem $disk, string $path): string
    {
        /** @var FilesystemAdapter $adapter */
        $adapter = $disk;

        return (string) $adapter->url($path);
    }
}
