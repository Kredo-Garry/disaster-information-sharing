<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


// =========================================================
// DIShiP: PHIVOLCS scheduled fetch (daily)
// Laravel 11: schedule is defined here (no app/Console/Kernel.php)
// =========================================================

$ca = '/usr/local/etc/ssl/cacert_plus_phivolcs.pem';

// 1日1回：時間は任意。ここでは 03:00 から数分ずらして順番に実行
Schedule::command("phivolcs:fetch-earthquakes --limit=10 --cafile={$ca}")
    ->dailyAt('03:00')
    ->withoutOverlapping(120)
    ->runInBackground();

Schedule::command("phivolcs:fetch-tsunami --cafile={$ca}")
    ->dailyAt('03:05')
    ->withoutOverlapping(120)
    ->runInBackground();

Schedule::command("phivolcs:fetch-volcano-alerts --limit=5 --cafile={$ca}")
    ->dailyAt('03:10')
    ->withoutOverlapping(120)
    ->runInBackground();
