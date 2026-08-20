<?php

namespace App\Jobs;

use App\Models\Schedule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendScheduleReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Schedule $schedule
    ) {}

    public function handle(): void
    {
        Log::info("Reminder: jadwal '{$this->schedule->title}' akan dimulai jam {$this->schedule->start_time}", [
            'user_id' => $this->schedule->user_id,
            'schedule_id' => $this->schedule->id,
        ]);
    }
}