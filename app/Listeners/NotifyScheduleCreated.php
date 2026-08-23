<?php

namespace App\Listeners;

use App\Events\ScheduleWasCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class NotifyScheduleCreated implements ShouldQueue
{
    public function handle(ScheduleWasCreated $event): void
    {
        Log::info("Notifikasi: jadwal '{$event->schedule->title}' berhasil dibuat, akan dimulai {$event->schedule->study_date}");
    }
}