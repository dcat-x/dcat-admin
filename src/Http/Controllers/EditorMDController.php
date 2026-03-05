<?php

namespace Dcat\Admin\Http\Controllers;

use Illuminate\Filesystem\FilesystemAdapter;
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
     * @return \Illuminate\Contracts\Filesystem\Filesystem|FilesystemAdapter
     */
    protected function disk()
    {
        $disk = request()->get('disk') ?: config('admin.upload.disk');

        return Storage::disk($disk);
    }

    protected function putFileAs($disk, string $dir, UploadedFile $file, string $name): void
    {
        call_user_func([$disk, 'putFileAs'], $dir, $file, $name);
    }

    protected function diskUrl($disk, string $path): string
    {
        return (string) call_user_func([$disk, 'url'], $path);
    }
}
