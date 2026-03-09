<?php

namespace Dcat\Admin\Console;

use Dcat\Admin\Support\ConfigHealthInspector;
use Illuminate\Console\Command;

class HealthCheckCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'admin:health-check {--json : Output issues as JSON}';

    /**
     * @var string
     */
    protected $description = 'Check admin menu/permission configuration health';

    public function handle(ConfigHealthInspector $inspector)
    {
        $issues = $inspector->inspect();

        if ($this->option('json')) {
            $this->line(json_encode([
                'ok' => count($issues) === 0,
                'count' => count($issues),
                'issues' => $issues,
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

            return 0;
        }

        if ($issues === []) {
            $this->info('No config issues detected.');

            return 0;
        }

        $this->warn(sprintf('Detected %d potential config issue(s):', count($issues)));

        foreach ($issues as $index => $issue) {
            $this->line(sprintf(
                '%d. [%s] %s (ids: %s)',
                $index + 1,
                $issue['type'] ?? 'unknown',
                $issue['message'] ?? '',
                implode(',', (array) ($issue['ids'] ?? []))
            ));
        }

        return 1;
    }
}
