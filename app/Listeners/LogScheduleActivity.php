<?php

namespace App\Listeners;

use App\Events\ScheduleWasCreated;
use Illuminate\Support\Facades\Log;

class LogScheduleActivity
{
    public function handle(ScheduleWasCreated $event): void
    {
        Log::info("Aktivitas: user {$event->schedule->user_id} membuat jadwal baru '{$event->schedule->title}'");
    }
}