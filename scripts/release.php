#!/usr/bin/env php
<?php

/**
 * 自动发版脚本
 *
 * 用法:
 *   php scripts/release.php <version> [--dry-run]
 *
 * 示例:
 *   php scripts/release.php 1.2.0
 *   php scripts/release.php 1.2.0 --dry-run
 *
 * 功能:
 *   1. 更新 src/Admin.php 中的 VERSION 常量
 *   2. 更新 CHANGELOG.md 添加新版本记录
 *   3. 提交更改并创建 git tag
 *   4. 推送到远程仓库
 */
class Release
{
    private string $version;

    private bool $dryRun;

    private string $rootPath;

    private string $date;

    public function __construct(string $version, bool $dryRun = false)
    {
        $this->version = ltrim($version, 'v');
        $this->dryRun = $dryRun;
        $this->rootPath = dirname(__DIR__);
        $this->date = date('Y-m-d');
    }

    public function run(): int
    {
        $this->info("发布版本 v{$this->version}");

        if ($this->dryRun) {
            $this->warning('DRY RUN 模式 - 不会实际执行任何操作');
        }

        // 检查工作目录是否干净
        if (! $this->isWorkingDirectoryClean()) {
            $this->error('工作目录有未提交的更改，请先提交或暂存');

            return 1;
        }

        // 检查版本格式
        if (! $this->isValidVersion()) {
            $this->error('无效的版本格式，请使用语义化版本 (如: 1.2.0)');

            return 1;
        }

        // 检查版本是否已存在
        if ($this->tagExists()) {
            $this->error("标签 v{$this->version} 已存在");

            return 1;
        }

        // 获取更新日志
        $changelog = $this->getChangelogFromGit();

        // 更新 Admin.php
        $this->updateAdminVersion();

        // 更新 CHANGELOG.md
        $this->updateChangelog($changelog);

        // 提交更改
        $this->commitChanges();

        // 创建标签
        $this->createTag();

        // 推送
        $this->push();

        $this->success("版本 v{$this->version} 发布完成！");
        $this->info("GitHub Release: https://github.com/dcat-x/dcat-admin/releases/tag/v{$this->version}");

        return 0;
    }

    private function isWorkingDirectoryClean(): bool
    {
        $output = shell_exec('git status --porcelain') ?? '';

        return empty(trim($output));
    }

    private function isValidVersion(): bool
    {
        return preg_match('/^\d+\.\d+\.\d+(-[\w.]+)?$/', $this->version) === 1;
    }

    private function tagExists(): bool
    {
        $output = shell_exec("git tag -l 'v{$this->version}'") ?? '';

        return ! empty(trim($output));
    }

    private function getChangelogFromGit(): array
    {
        // 获取上一个版本标签
        $lastTag = trim(shell_exec('git describe --tags --abbrev=0 2>/dev/null') ?: '');

        if (empty($lastTag)) {
            $range = 'HEAD';
        } else {
            $range = "{$lastTag}..HEAD";
        }

        // 获取提交日志
        $commits = shell_exec("git log {$range} --pretty=format:'%s' --no-merges 2>/dev/null");
        $commits = array_filter(explode("\n", $commits ?: ''));

        // 按类型分类
        $changelog = [
            'Added' => [],
            'Changed' => [],
            'Fixed' => [],
            'Removed' => [],
            'Other' => [],
        ];

        foreach ($commits as $commit) {
            $commit = trim($commit);
            if (empty($commit)) {
                continue;
            }

            // 跳过版本更新提交
            if (preg_match('/^(chore|release).*版本/', $commit)) {
                continue;
            }

            // 解析 Conventional Commits
            if (preg_match('/^(feat|feature)(\(.+?\))?:\s*(.+)$/i', $commit, $matches)) {
                $changelog['Added'][] = $matches[3];
            } elseif (preg_match('/^fix(\(.+?\))?:\s*(.+)$/i', $commit, $matches)) {
                $changelog['Fixed'][] = $matches[2];
            } elseif (preg_match('/^(refactor|perf|style)(\(.+?\))?:\s*(.+)$/i', $commit, $matches)) {
                $changelog['Changed'][] = $matches[3];
            } elseif (preg_match('/^(docs|test|ci|chore)(\(.+?\))?:\s*(.+)$/i', $commit, $matches)) {
                // 跳过文档、测试、CI 相关提交
                continue;
            } elseif (preg_match('/^remove(\(.+?\))?:\s*(.+)$/i', $commit, $matches)) {
                $changelog['Removed'][] = $matches[2];
            } else {
                $changelog['Other'][] = $commit;
            }
        }

        return array_filter($changelog);
    }

    private function updateAdminVersion(): void
    {
        $file = $this->rootPath.'/src/Admin.php';
        $content = file_get_contents($file);

        $newContent = preg_replace(
            "/const VERSION = '[^']+';/",
            "const VERSION = '{$this->version}';",
            $content
        );

        if ($this->dryRun) {
            $this->info("[DRY RUN] 更新 Admin.php VERSION 为 {$this->version}");
        } else {
            file_put_contents($file, $newContent);
            $this->info("已更新 Admin.php VERSION 为 {$this->version}");
        }
    }

    private function updateChangelog(array $changelog): void
    {
        $file = $this->rootPath.'/CHANGELOG.md';
        $content = file_get_contents($file);

        // 构建新版本的更新日志
        $newEntry = "\n## [{$this->version}] - {$this->date}\n";

        foreach ($changelog as $type => $items) {
            if (empty($items)) {
                continue;
            }
            $newEntry .= "\n### {$type}\n\n";
            foreach ($items as $item) {
                $newEntry .= "- {$item}\n";
            }
        }

        // 如果没有任何更新，添加占位符
        if (count(array_filter($changelog)) === 0) {
            $newEntry .= "\n### Changed\n\n- 版本更新\n";
        }

        // 在 [Unreleased] 后插入新版本
        $content = preg_replace(
            '/## \[Unreleased\]\n/',
            "## [Unreleased]\n{$newEntry}",
            $content
        );

        // 获取上一个版本用于更新链接
        $lastTag = trim(shell_exec('git describe --tags --abbrev=0 2>/dev/null') ?: 'v1.0.0');
        $lastVersion = ltrim($lastTag, 'v');

        // 更新底部链接
        $content = preg_replace(
            '/\[Unreleased\]: (.+?)compare\/v[\d.]+\.\.\.HEAD/',
            "[Unreleased]: $1compare/v{$this->version}...HEAD",
            $content
        );

        // 添加新版本链接
        $newLink = "[{$this->version}]: https://github.com/dcat-x/dcat-admin/compare/v{$lastVersion}...v{$this->version}";
        $content = preg_replace(
            '/(\[Unreleased\]: .+\n)/',
            "$1{$newLink}\n",
            $content
        );

        if ($this->dryRun) {
            $this->info('[DRY RUN] 更新 CHANGELOG.md');
            $this->info("新增条目:\n{$newEntry}");
        } else {
            file_put_contents($file, $content);
            $this->info('已更新 CHANGELOG.md');
        }
    }

    private function commitChanges(): void
    {
        $message = "chore: 更新版本号至 v{$this->version}";

        if ($this->dryRun) {
            $this->info("[DRY RUN] git add && git commit -m \"{$message}\"");
        } else {
            shell_exec('git add src/Admin.php CHANGELOG.md');
            shell_exec("git commit -m \"{$message}\"");
            $this->info('已提交更改');
        }
    }

    private function createTag(): void
    {
        if ($this->dryRun) {
            $this->info("[DRY RUN] git tag -a v{$this->version}");
        } else {
            shell_exec("git tag -a v{$this->version} -m 'Release v{$this->version}'");
            $this->info("已创建标签 v{$this->version}");
        }
    }

    private function push(): void
    {
        if ($this->dryRun) {
            $this->info('[DRY RUN] git push origin main --tags');
        } else {
            shell_exec('git push origin main --tags');
            $this->info('已推送到远程仓库');
        }
    }

    private function info(string $message): void
    {
        echo "\033[34m[INFO]\033[0m {$message}\n";
    }

    private function success(string $message): void
    {
        echo "\033[32m[SUCCESS]\033[0m {$message}\n";
    }

    private function warning(string $message): void
    {
        echo "\033[33m[WARNING]\033[0m {$message}\n";
    }

    private function error(string $message): void
    {
        echo "\033[31m[ERROR]\033[0m {$message}\n";
    }
}

// 主程序
if ($argc < 2) {
    echo "用法: php scripts/release.php <version> [--dry-run]\n";
    echo "示例: php scripts/release.php 1.2.0\n";
    exit(1);
}

$version = $argv[1];
$dryRun = in_array('--dry-run', $argv);

$release = new Release($version, $dryRun);
exit($release->run());
