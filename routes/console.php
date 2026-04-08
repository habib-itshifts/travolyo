<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Modules\Hotel\Jobs\SyncHyperguestHotelsJob;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Sync Hyperguest hotels every night at 2:00 AM
// Fetches hotel list + property-static data and upserts into the hotels table
Schedule::job(new SyncHyperguestHotelsJob())->dailyAt('02:00');
