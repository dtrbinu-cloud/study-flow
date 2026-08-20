<?php

use App\Jobs\SendScheduleReminderJob;
use App\Models\Schedule;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
