<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class PhivolcsFetchController extends Controller
{
    private string $cafile = '/usr/local/etc/ssl/cacert_plus_phivolcs.pem';

    public function index()
    {
        return view('admin.phivolcs');
    }

    public function fetchAll(Request $request)
    {
        $results = [];

        $results[] = $this->runCmd('phivolcs:fetch-earthquakes', [
            '--limit' => 10,
            '--cafile' => $this->cafile,
        ]);

        $results[] = $this->runCmd('phivolcs:fetch-tsunami', [
            '--cafile' => $this->cafile,
        ]);

        $results[] = $this->runCmd('phivolcs:fetch-volcano-alerts', [
            '--limit' => 5,
            '--cafile' => $this->cafile,
        ]);

        return back()->with('phivolcs_results', $results);
    }

    public function fetchEarthquakes(Request $request)
    {
        $result = $this->runCmd('phivolcs:fetch-earthquakes', [
            '--limit' => 10,
            '--cafile' => $this->cafile,
        ]);
        return back()->with('phivolcs_results', [$result]);
    }

    public function fetchTsunami(Request $request)
    {
        $result = $this->runCmd('phivolcs:fetch-tsunami', [
            '--cafile' => $this->cafile,
        ]);
        return back()->with('phivolcs_results', [$result]);
    }

    public function fetchVolcano(Request $request)
    {
        $result = $this->runCmd('phivolcs:fetch-volcano-alerts', [
            '--limit' => 5,
            '--cafile' => $this->cafile,
        ]);
        return back()->with('phivolcs_results', [$result]);
    }

    private function runCmd(string $name, array $options = []): array
    {
        $startedAt = now()->toDateTimeString();

        try {
            $exitCode = Artisan::call($name, $options);
            $output = trim(Artisan::output());

            return [
                'command' => $this->formatCmd($name, $options),
                'started_at' => $startedAt,
                'exit_code' => $exitCode,
                'ok' => ($exitCode === 0),
                'output' => $output,
            ];
        } catch (\Throwable $e) {
            return [
                'command' => $this->formatCmd($name, $options),
                'started_at' => $startedAt,
                'exit_code' => 1,
                'ok' => false,
                'output' => $e->getMessage(),
            ];
        }
    }

    private function formatCmd(string $name, array $options): string
    {
        $parts = ["php artisan {$name}"];
        foreach ($options as $k => $v) {
            if (is_bool($v)) {
                if ($v) $parts[] = $k;
                continue;
            }
            $parts[] = "{$k}=" . escapeshellarg((string) $v);
        }
        return implode(' ', $parts);
    }
}
