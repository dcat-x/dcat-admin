<?php

declare(strict_types=1);

namespace Dcat\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class EditorMDController
{
    public function upload(Request $request)
    {
        $file = $request->file('editormd-image-file');
        $dir = trim($request->get('dir'), '/');
        $disk = $this->disk();

        $newName = $this->generateNewName($file);

        $this->putFileAs($disk, $dir, $file, $newName);

        return ['success' => 1, 'url' => $this->diskUrl($disk, "{$dir}/$newName")];
    }

    protected function generateNewName(UploadedFile $file)
    {
        return uniqid(md5($file->getClientOriginalName())).'.'.$file->getClientOriginalExtension();
    }

    /**
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected function disk()
    {
        $disk = request()->get('disk') ?: config('admin.upload.disk');

        return Storage::disk($disk);
    }

    protected function putFileAs(\Illuminate\Contracts\Filesystem\Filesystem $disk, string $dir, UploadedFile $file, string $name): void
    {
        /** @var \Illuminate\Http\UploadedFile $uploadedFile */
        $uploadedFile = $file;
        $disk->putFileAs($dir, $uploadedFile, $name);
    }

    protected function diskUrl(\Illuminate\Contracts\Filesystem\Filesystem $disk, string $path): string
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $adapter */
        $adapter = $disk;

        return (string) $adapter->url($path);
    }
}
