<?php

declare(strict_types=1);

namespace Dcat\Admin\Console;

use Dcat\Admin\Support\ConfigHealthInspector;
use Illuminate\Console\Command;

class HealthCheckCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'admin:health-check
                            {--json : Output issues as JSON}
                            {--scope=all : Check scope: all|menu|permission}
                            {--fail-on=warning : Exit non-zero on: never|warning|error}
                            {--refresh : Bypass health-check cache}
                            {--quiet : Do not print issue details}';

    /**
     * @var string
     */
    protected $description = 'Check admin menu/permission configuration health';

    public function handle(ConfigHealthInspector $inspector)
    {
        $scope = strtolower((string) $this->option('scope'));
        if (! in_array($scope, ['all', 'menu', 'permission'], true)) {
            $this->error('Invalid --scope option. Allowed: all, menu, permission.');

            return 2;
        }

        $failOn = strtolower((string) $this->option('fail-on'));
        if (! in_array($failOn, ['never', 'warning', 'error'], true)) {
            $this->error('Invalid --fail-on option. Allowed: never, warning, error.');

            return 2;
        }

        $issues = $inspector->inspectByScope($scope, (bool) $this->option('refresh'));
        $exitCode = $this->resolveExitCode($issues, $failOn);

        if ($this->option('json')) {
            $this->line(json_encode([
                'ok' => count($issues) === 0,
                'count' => count($issues),
                'scope' => $scope,
                'fail_on' => $failOn,
                'refresh' => (bool) $this->option('refresh'),
                'issues' => $issues,
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

            return $exitCode;
        }

        if ($issues === [] && ! $this->option('quiet')) {
            $this->info('No config issues detected.');
        }

        if ($issues !== [] && ! $this->option('quiet')) {
            $this->warn(sprintf('Detected %d potential config issue(s):', count($issues)));

            foreach ($issues as $index => $issue) {
                $this->line(sprintf(
                    '%d. [%s/%s] %s (ids: %s)',
                    $index + 1,
                    $issue['severity'] ?? 'warning',
                    $issue['type'] ?? 'unknown',
                    $issue['message'] ?? '',
                    implode(',', (array) ($issue['ids'] ?? []))
                ));
            }
        }

        return $exitCode;
    }

    /**
     * @param  array<int, array<string, mixed>>  $issues
     */
    protected function resolveExitCode(array $issues, string $failOn): int
    {
        if ($failOn === 'never') {
            return 0;
        }

        if ($issues === []) {
            return 0;
        }

        if ($failOn === 'warning') {
            return 1;
        }

        foreach ($issues as $issue) {
            if (($issue['severity'] ?? 'warning') === 'error') {
                return 1;
            }
        }

        return 0;
    }
}
