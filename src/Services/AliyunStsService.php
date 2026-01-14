<?php

namespace Dcat\Admin\Services;

use Exception;
use Illuminate\Support\Facades\Log;

/**
 * 阿里云 STS 服务类
 *
 * 用于生成 OSS 临时授权凭证，实现前端直连 OSS 上传。
 *
 * 配置要求（在 config/admin.php 或 config/filesystems.php 中配置）：
 * - OSS_ACCESS_KEY_ID: 阿里云 AccessKey ID
 * - OSS_ACCESS_KEY_SECRET: 阿里云 AccessKey Secret
 * - OSS_BUCKET: OSS Bucket 名称
 * - OSS_ENDPOINT: OSS 端点地址
 * - OSS_STS_ROLE_ARN: RAM 角色 ARN
 * - OSS_STS_REGION_ID: STS 服务区域
 *
 * 依赖包：
 * composer require alibabacloud/sts-20150401
 */
class AliyunStsService
{
    /**
     * 获取 STS 临时凭证.
     *
     * @param  string|null  $uploadDir  允许上传的目录前缀（为空则不限制）
     * @return array{AccessKeyId: string, AccessKeySecret: string, SecurityToken: string, Expiration: string}
     *
     * @throws Exception
     */
    public function getStsToken(?string $uploadDir = null): array
    {
        // 检查依赖是否安装
        if (! class_exists(\AlibabaCloud\SDK\Sts\V20150401\Sts::class)) {
            throw new Exception('请先安装阿里云 STS SDK: composer require alibabacloud/sts-20150401');
        }

        try {
            $config = $this->getOssConfig();

            // 从 admin 配置或 filesystems 配置获取 STS 配置
            $stsConfig = config('admin.upload.oss.sts', []);
            $accessKeyId = $stsConfig['access_key_id'] ?? config('filesystems.disks.oss.access_key_id');
            $accessKeySecret = $stsConfig['access_key_secret'] ?? config('filesystems.disks.oss.access_key_secret');
            $roleArn = $stsConfig['role_arn'] ?? config('filesystems.disks.oss.sts_role_arn');
            $regionId = $stsConfig['region_id'] ?? config('filesystems.disks.oss.sts_region_id', 'cn-hangzhou');
            $duration = $stsConfig['duration'] ?? config('filesystems.disks.oss.sts_duration', 3600);
            $policy = $stsConfig['policy'] ?? config('filesystems.disks.oss.sts_policy');

            if (! $accessKeyId || ! $accessKeySecret || ! $roleArn) {
                throw new Exception('OSS STS 配置不完整，请检查 access_key_id, access_key_secret, role_arn');
            }

            // 创建 STS 客户端配置
            $clientConfig = new \Darabonba\OpenApi\Models\Config([
                'accessKeyId' => $accessKeyId,
                'accessKeySecret' => $accessKeySecret,
                'regionId' => $regionId,
            ]);

            $client = new \AlibabaCloud\SDK\Sts\V20150401\Sts($clientConfig);

            // 构建请求
            $request = new \AlibabaCloud\SDK\Sts\V20150401\Models\AssumeRoleRequest([
                'roleArn' => $roleArn,
                'roleSessionName' => 'oss-upload-'.time(),
                'durationSeconds' => $duration,
            ]);

            // 自定义策略（限制上传目录和权限）
            if ($uploadDir || $policy) {
                $request->policy = $policy ?? $this->generateUploadPolicy($config['bucket'], $uploadDir);
            }

            // 调用 STS API
            $response = $client->assumeRole($request);

            if (! $response || ! $response->body || ! $response->body->credentials) {
                throw new Exception('Failed to get STS credentials');
            }

            $credentials = $response->body->credentials;

            return [
                'AccessKeyId' => $credentials->accessKeyId,
                'AccessKeySecret' => $credentials->accessKeySecret,
                'SecurityToken' => $credentials->securityToken,
                'Expiration' => $credentials->expiration,
            ];
        } catch (Exception $e) {
            Log::error('获取 STS 临时凭证失败', [
                'error' => $e->getMessage(),
                'upload_dir' => $uploadDir,
            ]);

            throw new Exception('获取 STS 凭证失败: '.$e->getMessage());
        }
    }

    /**
     * 生成上传策略（限制只能上传到指定目录）.
     */
    protected function generateUploadPolicy(string $bucket, ?string $uploadDir = null): string
    {
        $resources = $uploadDir
            ? ["acs:oss:*:*:{$bucket}/{$uploadDir}*"]
            : ["acs:oss:*:*:{$bucket}/*"];

        $policy = [
            'Version' => '1',
            'Statement' => [
                [
                    'Effect' => 'Allow',
                    'Action' => [
                        'oss:PutObject',
                    ],
                    'Resource' => $resources,
                ],
            ],
        ];

        return json_encode($policy);
    }

    /**
     * 获取 OSS 配置信息（用于前端初始化）.
     */
    public function getOssConfig(): array
    {
        // 优先从 admin 配置获取，否则从 filesystems 配置获取
        $adminOssConfig = config('admin.upload.oss', []);
        $filesystemConfig = config('filesystems.disks.oss', []);

        $bucket = $adminOssConfig['bucket'] ?? $filesystemConfig['bucket'] ?? '';
        $endpoint = $adminOssConfig['endpoint'] ?? $filesystemConfig['endpoint'] ?? '';
        $cdnDomain = $adminOssConfig['cdn_domain'] ?? $filesystemConfig['cdnDomain'] ?? null;

        // 从 endpoint 提取 region
        // endpoint 格式: oss-cn-hongkong.aliyuncs.com 或 https://oss-cn-hongkong.aliyuncs.com
        $region = str_replace(['https://', 'http://', '.aliyuncs.com'], '', $endpoint);

        return [
            'region' => $region,
            'bucket' => $bucket,
            'endpoint' => str_replace(['https://', 'http://'], '', $endpoint),
            'cdn_domain' => $cdnDomain,
        ];
    }
}
