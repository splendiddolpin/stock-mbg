<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule; // Pastikan baris ini ada

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// TAMBAHKAN BARIS INI UNTUK JADWAL ROBOT KITA:
Schedule::command('app:auto-reset-menu')->dailyAt('12:24');