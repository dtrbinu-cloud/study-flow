<?php

use App\Jobs\SendScheduleReminderJob;
use App\Models\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule as TaskSchedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

TaskSchedule::call(function () {
    $todaySchedules = Schedule::whereDate('study_date', today())
        ->where('status', 'planned')
        ->where('reminder_sent', false)
        ->get();

    foreach ($todaySchedules as $schedule) {
        SendScheduleReminderJob::dispatch($schedule);
        $schedule->update(['reminder_sent' => true]);
    }
})->dailyAt('07:00');