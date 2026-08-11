<?php

namespace App\Repositories;

use App\Models\Schedule;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class ScheduleRepository
{
    public function getAllByUser(User $user): Collection
    {
        return $user->schedules()
            ->with('subject')
            ->orderBy('study_date')
            ->orderBy('start_time')
            ->get();
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