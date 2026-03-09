<?php

namespace Dcat\Admin\Support\Concerns;

use Illuminate\Support\Str;

trait ControlsLogEmission
{
    /**
     * @var int|null
     */
    protected static $logControlRequestHash;

    /**
     * @var array<string, array<string, mixed>>
     */
    protected static array $logControlConfigCache = [];

    /**
     * @var array<string, bool>
     */
    protected static array $logControlSampleCache = [];

    protected function shouldEmitLog(string $group, ?string $path = null): bool
    {
        $this->resetLogControlCacheWhenRequestChanged();

        $path = $path !== null ? ltrim($path, '/') : null;
        $config = $this->getLogControlConfig($group);

        if ($path !== null) {
            $only = (array) ($config['only_paths'] ?? []);
            if ($only !== [] && ! $this->matchesAnyPathPattern($path, $only)) {
                return false;
            }

            $except = (array) ($config['except_paths'] ?? []);
            if ($except !== [] && $this->matchesAnyPathPattern($path, $except)) {
                return false;
            }
        }

        $sampleRate = (float) ($config['sample_rate'] ?? 1);

        if ($sampleRate >= 1) {
            return true;
        }

        if ($sampleRate <= 0) {
            return false;
        }

        return $this->shouldSampleLog($group, $sampleRate);
    }

    /**
     * @param  array<int, string>  $patterns
     */
    protected function matchesAnyPathPattern(string $path, array $patterns): bool
    {
        foreach ($patterns as $pattern) {
            $pattern = trim((string) $pattern);
            if ($pattern === '') {
                continue;
            }

            if (Str::is($pattern, $path)) {
                return true;
            }
        }

        return false;
    }

    protected function resolveTraceId(): ?string
    {
        if (! app()->bound('request')) {
            return null;
        }

        $request = request();
        $traceId = $request->headers->get('X-Request-Id')
            ?: $request->headers->get('X-Trace-Id')
            ?: $request->attributes->get('_admin_trace_id');

        if (! $traceId) {
            $traceId = (string) Str::uuid();
            $request->attributes->set('_admin_trace_id', $traceId);
        }

        return $traceId;
    }

    protected function shouldSampleLog(string $group, float $sampleRate): bool
    {
        $traceId = $this->resolveTraceId();
        if (! $traceId) {
            return mt_rand() / mt_getrandmax() <= $sampleRate;
        }

        $sampleKey = $group.':'.$traceId.':'.(string) $sampleRate;
        if (array_key_exists($sampleKey, static::$logControlSampleCache)) {
            return static::$logControlSampleCache[$sampleKey];
        }

        $threshold = (int) round($sampleRate * 10000);
        $hash = abs(crc32($sampleKey)) % 10000;

        return static::$logControlSampleCache[$sampleKey] = $hash < $threshold;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getLogControlConfig(string $group): array
    {
        if (array_key_exists($group, static::$logControlConfigCache)) {
            return static::$logControlConfigCache[$group];
        }

        return static::$logControlConfigCache[$group] = (array) config("admin.log_control.{$group}", []);
    }

    protected function resetLogControlCacheWhenRequestChanged(): void
    {
        if (! app()->bound('request')) {
            return;
        }

        $requestHash = spl_object_id(request());

        if (static::$logControlRequestHash === $requestHash) {
            return;
        }

        static::$logControlRequestHash = $requestHash;
        static::$logControlConfigCache = [];
        static::$logControlSampleCache = [];
    }
}
