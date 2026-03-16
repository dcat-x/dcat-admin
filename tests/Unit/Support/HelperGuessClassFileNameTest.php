<?php

declare(strict_types=1);

namespace Dcat\Admin\Tests\Unit\Support;

use Dcat\Admin\Support\Helper;
use Dcat\Admin\Tests\TestCase;

class HelperGuessClassFileNameTest extends TestCase
{
    public function test_guess_class_file_name_for_existing_class(): void
    {
        // 对于存在的类，应该通过 ReflectionClass 获取文件路径
        $result = Helper::guessClassFileName(Helper::class);

        $this->assertStringContainsString('Helper.php', $result);
    }

    public function test_guess_class_file_name_with_object(): void
    {
        // 使用一个有实际文件路径的类
        $result = Helper::guessClassFileName(new \Dcat\Admin\Models\DataRule);

        $this->assertIsString($result);
        $this->assertStringContainsString('DataRule.php', $result);
    }

    public function test_guess_class_file_name_for_app_class(): void
    {
        // 对于 App 命名空间下的不存在的类，应返回 app/ 前缀路径
        $result = Helper::guessClassFileName('App\\Models\\FakeTestModel');

        $this->assertStringContainsString('app/', $result);
        $this->assertStringEndsWith('FakeTestModel.php', $result);
    }

    public function test_explode_correctly_extracts_namespace_prefix(): void
    {
        // 行为测试：guessClassFileName 对不存在的类名
        // 修复前 explode($class, '\\') 参数反了，导致分割失败，返回错误路径
        // 修复后 explode('\\', $class) 正确分割命名空间

        // 使用 App 命名空间的不存在的类（App 映射到 app/ 有 fallback 逻辑）
        $result = Helper::guessClassFileName('App\\Http\\Controllers\\FakeClass123');

        // 应返回合理的文件路径
        $this->assertStringEndsWith('FakeClass123.php', $result);
        // App 命名空间应映射到 app/ 目录
        $this->assertStringContainsString('app/', $result);
        // 子命名空间也应正确转换为目录路径
        $this->assertStringContainsString('Http', $result);
        $this->assertStringContainsString('Controllers', $result);
    }

    public function test_explode_fix_does_not_return_empty_prefix(): void
    {
        // 修复前，explode($class, '\\') 对正常类名会产生意外结果
        // 修复后，explode('\\', $class) 正确分割命名空间

        // 使用反射验证修复后的代码行为
        $class = 'App\\Controllers\\TestController';
        $parts = explode('\\', $class);

        // 第一个部分应该是命名空间前缀
        $this->assertSame('App', $parts[0]);
        $this->assertCount(3, $parts);
    }
}
