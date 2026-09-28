<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function (): void {
    $this->comment(
        \Illuminate\Foundation\Inspiring::quote()
    );
})->purpose('Display an inspiring quote');

Schedule::command('queue:prune-batches --hours=48')
    ->dailyAt('02:00');

Schedule::command('queue:prune-failed --hours=168')
    ->dailyAt('02:15');

Schedule::command('notifications:table-prune')
    ->dailyAt('03:00')
    ->when(
        fn (): bool => Artisan::all()[
            'notifications:table-prune'
        ] ?? false
    );