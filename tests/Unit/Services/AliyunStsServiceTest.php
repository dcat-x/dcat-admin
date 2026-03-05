<?php

namespace Dcat\Admin\Tests\Unit\Services;

use Dcat\Admin\Services\AliyunStsService;
use Dcat\Admin\Tests\TestCase;

class AliyunStsServiceTest extends TestCase
{
    public function test_get_oss_config_prefers_admin_config_and_normalizes_endpoint(): void
    {
        $this->app['config']->set('admin.upload.oss', [
            'bucket' => 'admin-bucket',
            'endpoint' => 'https://oss-cn-hangzhou.aliyuncs.com',
            'cdn_domain' => 'https://cdn.example.com',
        ]);
        $this->app['config']->set('filesystems.disks.oss', [
            'bucket' => 'fs-bucket',
            'endpoint' => 'https://oss-cn-shanghai.aliyuncs.com',
            'cdnDomain' => null,
        ]);

        $service = new AliyunStsService;
        $config = $service->getOssConfig();

        $this->assertSame('admin-bucket', $config['bucket']);
        $this->assertSame('oss-cn-hangzhou.aliyuncs.com', $config['endpoint']);
        $this->assertSame('oss-cn-hangzhou', $config['region']);
        $this->assertSame('https://cdn.example.com', $config['cdn_domain']);
    }

    public function test_get_oss_config_falls_back_to_filesystem_config(): void
    {
        $this->app['config']->set('admin.upload.oss', []);
        $this->app['config']->set('filesystems.disks.oss', [
            'bucket' => 'fs-bucket',
            'endpoint' => 'http://oss-cn-shenzhen.aliyuncs.com',
            'cdnDomain' => 'https://cdn.fs.example.com',
        ]);

        $service = new AliyunStsService;
        $config = $service->getOssConfig();

        $this->assertSame('fs-bucket', $config['bucket']);
        $this->assertSame('oss-cn-shenzhen.aliyuncs.com', $config['endpoint']);
        $this->assertSame('oss-cn-shenzhen', $config['region']);
        $this->assertSame('https://cdn.fs.example.com', $config['cdn_domain']);
    }

    public function test_generate_upload_policy_restricts_upload_directory_when_provided(): void
    {
        $service = new class extends AliyunStsService
        {
            public function exposeGenerateUploadPolicy(string $bucket, ?string $uploadDir = null): string
            {
                return $this->generateUploadPolicy($bucket, $uploadDir);
            }
        };

        $policy = $service->exposeGenerateUploadPolicy('my-bucket', 'images/2026/03/05/');
        $decoded = json_decode($policy, true);

        $this->assertSame('Allow', $decoded['Statement'][0]['Effect']);
        $this->assertSame('oss:PutObject', $decoded['Statement'][0]['Action'][0]);
        $this->assertSame('acs:oss:*:*:my-bucket/images/2026/03/05/*', $decoded['Statement'][0]['Resource'][0]);
    }

    public function test_get_sts_token_throws_exception_when_runtime_dependencies_or_config_are_not_ready(): void
    {
        $service = new AliyunStsService;

        try {
            $service->getStsToken('images/');
            $this->fail('Expected exception was not thrown.');
        } catch (\Exception $e) {
            $this->assertStringContainsString('STS', $e->getMessage());
        }
    }
}
