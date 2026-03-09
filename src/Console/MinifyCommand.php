<?php

namespace Dcat\Admin\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class MinifyCommand extends Command
{
    const ALL = 'all';

    const DEFAULT = 'default';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:minify {name} 
        {--color= : Theme color code} 
        {--publish : Publish assets files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Minify the CSS and JS';

    /**
     * @var array
     */
    protected $colors = [
        self::DEFAULT => '',
        'blue' => '#6d8be6',
        'blue-light' => '#62a8ea',
        'green' => '#4e9876',
    ];

    /**
     * @var string
     */
    protected $packagePath;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->packagePath = realpath(__DIR__.'/../..');

        $name = $this->argument('name');
        $publish = (bool) $this->option('publish');

        if ($name === static::ALL) {
            // 编译所有内置主题色
            $this->compileAllColors();

            if ($publish) {
                $this->publishAssets();
            }

            return;
        }

        $color = $this->getColor($name);

        $this->npmInstall();

        if ($name === static::DEFAULT) {
            $this->info("[{$name}] npm run prod...");
            $this->runProcess('npm run prod', 1800);
        } else {
            $env = [
                'THEME' => $name,
                'BUILD_THEME_ONLY' => '1',
            ];

            if ($color && (! isset($this->colors[$name]) || $color !== $this->colors[$name])) {
                $env['CUSTOM_THEME_PRIMARY'] = $color;
            }

            $this->info("[$name][$color] npm run production...");
            $this->runProcess('npm run production', 1800, $env);
        }

        if ($publish) {
            $this->publishAssets();
        }
    }

    /**
     * 编译所有内置主题.
     */
    protected function compileAllColors()
    {
        $this->npmInstall();

        $this->info('[default] npm run prod...');
        $this->runProcess('npm run prod', 1800);

        foreach ($this->colors as $name => $color) {
            if ($name === static::DEFAULT) {
                continue;
            }

            $this->info("[$name][$color] npm run production...");
            $this->runProcess('npm run production', 1800, [
                'THEME' => $name,
                'BUILD_THEME_ONLY' => '1',
            ]);
        }
    }

    /**
     * 发布静态资源.
     */
    protected function publishAssets()
    {
        $options = ['--provider' => 'Dcat\Admin\AdminServiceProvider', '--force' => true, '--tag' => 'dcat-admin-assets'];

        $this->call('vendor:publish', $options);
    }

    /**
     * 安装依赖.
     */
    protected function npmInstall()
    {
        if (is_dir($this->packagePath.'/node_modules')) {
            return;
        }

        $this->info('npm install...');

        $this->runProcess('npm install');
    }

    /**
     * 获取颜色.
     *
     * @param  string  $name
     * @return string
     */
    protected function getColor($name)
    {
        if ($name === static::DEFAULT) {
            return '';
        }

        INPUT_COLOR:

        $color = $this->option('color');

        if (! $color && isset($this->colors[$name])) {
            return $this->colors[$name];
        }

        if (! $color) {
            $color = $this->formatColor($this->ask('Please enter a color code(hex)'));
        }

        if (! $color) {
            goto INPUT_COLOR;
        }

        return $this->formatColor($color);
    }

    /**
     * @param  string  $color
     * @return string
     */
    protected function formatColor($color)
    {
        if ($color && ! Str::startsWith($color, '#')) {
            $color = "#$color";
        }

        return $color;
    }

    /**
     * 执行命令.
     *
     * @param  string  $command
     * @param  int  $timeout
     */
    protected function runProcess($command, $timeout = 1800, array $env = [])
    {
        $process = Process::fromShellCommandline(
            $command,
            $this->packagePath,
            $env
        );
        $process->setTimeout($timeout);

        $process->run(function ($type, $data) {
            if ($type === Process::ERR) {
                $this->warn($data);
            } else {
                $this->info($data);
            }
        });
    }
}
