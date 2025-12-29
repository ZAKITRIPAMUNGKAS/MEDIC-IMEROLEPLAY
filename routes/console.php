<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule expired duty sessions check every minute
Schedule::command('attendance:check-expired-sessions')
    ->everyMinute()
    ->withoutOverlapping()
    ->onOneServer();

// Schedule automatic payroll generation every Sunday at 23:59
Schedule::command('payroll:auto-generate')
    ->weeklyOn(0, '23:59') // Every Sunday at 23:59
    ->withoutOverlapping()
    ->onOneServer();
