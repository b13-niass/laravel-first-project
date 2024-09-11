<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

//Artisan::command('inspire', function () {
//    $this->comment(Inspiring::quote());
//})->purpose('Display an inspiring quote')->hourly();

Schedule::command('client:relance-cloud')->everyTenSeconds();
Schedule::command('app:save-to-mongo')->dailyAt('00:00');
Schedule::command('app:send-message')->weeklyOn(5, '14:00');
