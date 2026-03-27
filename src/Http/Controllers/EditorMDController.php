<?php

declare(strict_types=1);

namespace Dcat\Admin\Http\Controllers;

use Dcat\Admin\Http\Controllers\Concerns\UploadsFiles;
use Illuminate\Http\Request;

class EditorMDController
{
    use UploadsFiles;

    public function upload(Request $request)
    {
        $file = $this->validateUploadFile($request, 'editormd-image-file');
        $dir = trim((string) $request->get('dir'), '/');
        $disk = $this->disk();

        $newName = $this->generateNewName($file);

        $this->putFileAs($disk, $dir, $file, $newName);

        return ['success' => 1, 'url' => $this->diskUrl($disk, "{$dir}/$newName")];
    }
}
