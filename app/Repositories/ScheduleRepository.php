<?php

namespace App\Repositories;

use App\Models\Schedule;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class ScheduleRepository
{
    public function getAllByUser(User $user): Collection
    {
        $cacheKey = "schedules:user:{$user->id}";

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($user) {
            return $user->schedules()
                ->with('subject')
                ->orderBy('study_date')
                ->orderBy('start_time')
                ->get();
        });
    }

    public function create(User $user, array $data): Schedule
    {
        $schedule = $user->schedules()->create($data);
        $schedule->load('subject');
        return $schedule;
    }

    public function update(Schedule $schedule, array $data): Schedule
    {
        $schedule->update($data);
        $schedule->load('subject');
        return $schedule;
    }

    public function delete(Schedule $schedule): void
    {
        $schedule->delete();
    }
}