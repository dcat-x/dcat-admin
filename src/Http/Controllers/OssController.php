<?php

namespace Dcat\Admin\Http\Controllers;

use Carbon\Carbon;
use Dcat\Admin\Services\AliyunStsService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * OSS 控制器
 *
 * 提供 STS 临时凭证接口，支持前端直连 OSS 上传。
 */
class OssController extends AdminController
{
    /**
     * 获取 STS 临时凭证.
     */
    public function getStsToken(Request $request): JsonResponse
    {
        try {
            $stsService = app(AliyunStsService::class);

            // 获取上传目录（支持自定义目录）
            $customDir = $request->input('directory');
            $type = $request->input('type', 'file');

            if ($customDir) {
                // 使用自定义目录 + 日期路径
                $baseDir = $this->validateAndFormatDirectory($customDir);
                $datePath = $this->generateDatePath();
                $uploadDir = rtrim($baseDir, '/').'/'.$datePath;
            } else {
                // 使用默认的按日期分组目录（type/日期）
                $uploadDir = $this->generateUploadDir($type);
            }

            // 获取 STS 临时凭证
            $credentials = $stsService->getStsToken($uploadDir);

            // 获取 OSS 配置
            $ossConfig = $stsService->getOssConfig();

            return response()->json([
                'success' => true,
                'data' => [
                    'region' => $ossConfig['region'],
                    'bucket' => $ossConfig['bucket'],
                    'endpoint' => $ossConfig['endpoint'],
                    'cdn_domain' => $ossConfig['cdn_domain'] ?? null,
                    'credentials' => $credentials,
                    'upload_dir' => $uploadDir,
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * 生成上传目录路径（类型 + 日期）.
     */
    protected function generateUploadDir(string $type = 'file'): string
    {
        // 根据类型确定目录
        $baseDir = match ($type) {
            'image' => 'images',
            'file' => 'files',
            default => 'files',
        };

        $datePath = $this->generateDatePath();

        return "{$baseDir}/{$datePath}";
    }

    /**
     * 生成日期路径（YYYY/MM/DD/）.
     */
    protected function generateDatePath(): string
    {
        return Carbon::now()->format('Y/m/d').'/';
    }

    /**
     * 验证并格式化自定义目录.
     *
     * @throws Exception
     */
    protected function validateAndFormatDirectory(string $directory): string
    {
        // 允许的目录白名单（可通过配置文件自定义）
        $allowedPrefixes = config('admin.upload.oss.allowed_directories', [
            'files',
            'images',
            'documents',
            'videos',
            'apk',
            'app',
        ]);

        // 移除前后的斜杠，统一格式
        $directory = trim($directory, '/');

        // 空目录检查
        if (empty($directory)) {
            throw new Exception(__('admin.oss_upload.directory_empty', [], 'Directory cannot be empty'));
        }

        // 防止目录穿越攻击
        if (str_contains($directory, '..') || str_contains($directory, '\\')) {
            throw new Exception(__('admin.oss_upload.invalid_path', [], 'Invalid directory path'));
        }

        // 验证目录前缀是否在白名单中
        $prefix = explode('/', $directory)[0];
        if (! in_array($prefix, $allowedPrefixes)) {
            throw new Exception(__('admin.oss_upload.directory_not_allowed', [], 'Upload to this directory is not allowed'));
        }

        return $directory;
    }

    /**
     * 生成唯一文件名.
     */
    public function generateFilename(Request $request): JsonResponse
    {
        $extension = $request->input('extension', 'bin');
        $uploadDir = $this->generateUploadDir($request->input('type', 'file'));
        $filename = Str::random(32).'.'.$extension;

        return response()->json([
            'success' => true,
            'data' => [
                'filename' => $filename,
                'path' => $uploadDir.$filename,
            ],
        ]);
    }

    /**
     * 私有桶图片代理 - 生成签名 URL 并重定向.
     *
     * @param  string  $path  文件路径 (URL 编码)
     */
    public function privateImageProxy(string $path): RedirectResponse|JsonResponse
    {
        // URL 解码路径
        $path = urldecode($path);

        if (empty($path)) {
            return response()->json(['error' => 'Path is required'], 400);
        }

        // 安全检查：防止目录穿越
        if (str_contains($path, '..') || str_contains($path, '\\')) {
            return response()->json(['error' => 'Invalid path'], 400);
        }

        try {
            $diskName = config('admin.upload.oss.private_disk', 'oss-private');
            $disk = Storage::disk($diskName);
            $expireMinutes = config('admin.upload.oss.signed_url_expire', 60);
            $signedUrl = $disk->temporaryUrl($path, now()->addMinutes($expireMinutes));

            return redirect()->away($signedUrl);
        } catch (Exception $e) {
            \Log::warning('Failed to generate private image URL', [
                'path' => $path,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Failed to generate URL'], 500);
        }
    }
}
