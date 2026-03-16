<?php

declare(strict_types=1);

namespace Dcat\Admin\Http\Controllers;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class EditorMDController
{
    public function upload(Request $request)
    {
        $file = $request->file('editormd-image-file');
        $dir = trim((string) $request->get('dir'), '/');
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
     * @return Filesystem
     */
    protected function disk()
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
