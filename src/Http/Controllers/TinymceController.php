<?php

namespace Dcat\Admin\Http\Controllers;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class TinymceController
{
    public function upload(Request $request)
    {
        $file = $request->file('file');
        $dir = trim($request->get('dir'), '/');
        $disk = $this->disk();

        $newName = $this->generateNewName($file);

        $this->putFileAs($disk, $dir, $file, $newName);

        return ['location' => $this->diskUrl($disk, "{$dir}/$newName")];
    }

    protected function generateNewName(UploadedFile $file)
    {
        return uniqid(md5($file->getClientOriginalName())).'.'.$file->getClientOriginalExtension();
    }

    protected function disk(): FilesystemAdapter
    {
        $disk = request()->get('disk') ?: config('admin.upload.disk');

        return Storage::disk($disk);
    }

    protected function putFileAs(FilesystemAdapter $disk, string $dir, UploadedFile $file, string $name): void
    {
        $disk->putFileAs($dir, $file, $name);
    }

    protected function diskUrl(FilesystemAdapter $disk, string $path): string
    {
        return (string) $disk->url($path);
    }
}
