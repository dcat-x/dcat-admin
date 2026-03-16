<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\FuncCall\SetTypeToCastRector;
use Rector\CodeQuality\Rector\FuncCall\SimplifyRegexPatternRector;
use Rector\CodeQuality\Rector\FuncCall\SingleInArrayToCompareRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\Assign\RemoveDoubleAssignRector;
use Rector\DeadCode\Rector\Assign\RemoveUnusedVariableAssignRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveEmptyClassMethodRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUnusedPrivateMethodRector;
use Rector\Php81\Rector\FuncCall\NullToStrictStringFuncCallArgRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/src',
    ])
    ->withSkip([
        __DIR__.'/src/Scaffold/stubs',
        __DIR__.'/src/Console/stubs',
    ])
    ->withRules([
        // strict_types 类型安全
        NullToStrictStringFuncCallArgRector::class,
        // 代码质量
        SetTypeToCastRector::class,
        SimplifyRegexPatternRector::class,
        SingleInArrayToCompareRector::class,
        // 死代码清理
        RemoveDoubleAssignRector::class,
        RemoveUnusedVariableAssignRector::class,
        RemoveEmptyClassMethodRector::class,
        RemoveUnusedPrivateMethodRector::class,
    ]);
