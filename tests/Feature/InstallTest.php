<?php

namespace Dcat\Admin\Tests\Feature;

use Dcat\Admin\Tests\TestCase;
use Illuminate\Console\OutputStyle;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

/**
 * 安装功能测试.
 *
 * 验证 admin:install 命令的目录初始化逻辑
 */
class InstallTest extends TestCase
{
    protected string $testAdminDir;

    protected Filesystem $files;

    protected function setUp(): void
    {
        parent::setUp();

        $this->files = new Filesystem;
        $this->testAdminDir = sys_get_temp_dir().'/dcat_admin_test_'.uniqid();

        // 设置 admin.directory 指向临时目录
        $this->app['config']->set('admin.directory', $this->testAdminDir);
        $this->app['config']->set('admin.route.namespace', 'App\\Admin\\Controllers');
    }

    protected function tearDown(): void
    {
        // 清理临时目录
        if (is_dir($this->testAdminDir)) {
            $this->files->deleteDirectory($this->testAdminDir);
        }

        parent::tearDown();
    }

    public function test_admin_path_helper_returns_correct_path(): void
    {
        $path = admin_path();

        $this->assertNotEmpty($path);
        $this->assertStringContainsString('dcat_admin_test_', $path);
    }

    public function test_admin_path_helper_with_sub_path(): void
    {
        $path = admin_path('Controllers');

        $this->assertStringContainsString('Controllers', $path);
    }

    public function test_init_admin_directory_creates_structure(): void
    {
        // 直接调用 InstallCommand 的目录初始化（跳过数据库部分）
        $command = new \Dcat\Admin\Console\InstallCommand;
        $command->setLaravel($this->app);
        $command->setOutput(new OutputStyle(new ArrayInput([]), new NullOutput));

        // 通过反射调用 initAdminDirectory
        $reflection = new \ReflectionMethod($command, 'initAdminDirectory');
        $reflection->setAccessible(true);
        $reflection->invoke($command);

        // 验证目录结构
        $this->assertDirectoryExists($this->testAdminDir);
        $this->assertDirectoryExists($this->testAdminDir.'/Controllers');
        $this->assertDirectoryExists($this->testAdminDir.'/Metrics/Examples');

        // 验证关键文件
        $this->assertFileExists($this->testAdminDir.'/Controllers/HomeController.php');
        $this->assertFileExists($this->testAdminDir.'/Controllers/AuthController.php');
        $this->assertFileExists($this->testAdminDir.'/routes.php');
        $this->assertFileExists($this->testAdminDir.'/bootstrap.php');

        // 验证 Metric 文件
        $this->assertFileExists($this->testAdminDir.'/Metrics/Examples/NewDevices.php');
        $this->assertFileExists($this->testAdminDir.'/Metrics/Examples/NewUsers.php');
        $this->assertFileExists($this->testAdminDir.'/Metrics/Examples/ProductOrders.php');
        $this->assertFileExists($this->testAdminDir.'/Metrics/Examples/Sessions.php');
        $this->assertFileExists($this->testAdminDir.'/Metrics/Examples/Tickets.php');
    }

    public function test_generated_controllers_have_correct_namespace(): void
    {
        $command = new \Dcat\Admin\Console\InstallCommand;
        $command->setLaravel($this->app);
        $command->setOutput(new OutputStyle(new ArrayInput([]), new NullOutput));

        $reflection = new \ReflectionMethod($command, 'initAdminDirectory');
        $reflection->setAccessible(true);
        $reflection->invoke($command);

        $homeController = file_get_contents($this->testAdminDir.'/Controllers/HomeController.php');
        $this->assertStringContainsString('namespace App\\Admin\\Controllers', $homeController);

        $authController = file_get_contents($this->testAdminDir.'/Controllers/AuthController.php');
        $this->assertStringContainsString('namespace App\\Admin\\Controllers', $authController);
    }

    public function test_init_directory_skips_if_already_exists(): void
    {
        // 先创建目录
        $this->files->makeDirectory($this->testAdminDir, 0755, true);

        $command = new \Dcat\Admin\Console\InstallCommand;
        $command->setLaravel($this->app);
        $command->setOutput(new OutputStyle(new ArrayInput([]), new NullOutput));

        $reflection = new \ReflectionMethod($command, 'initAdminDirectory');
        $reflection->setAccessible(true);
        $reflection->invoke($command);

        // 目录已存在时，不应创建子目录
        $this->assertDirectoryDoesNotExist($this->testAdminDir.'/Controllers');
    }
}
