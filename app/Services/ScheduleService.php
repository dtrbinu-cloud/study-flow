<?php

namespace App\Services;

use App\Events\ScheduleWasCreated;
use App\Models\Schedule;
use App\Models\User;
use App\Repositories\ScheduleRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class ScheduleService
{
    public function __construct(
        protected ScheduleRepository $scheduleRepository
    ) {}

    public function getAll(User $user): Collection
    {
        return $this->scheduleRepository->getAllByUser($user);
    }

    public function create(User $user, array $data): Schedule
    {
        $schedule = $this->scheduleRepository->create($user, $data);
        Cache::forget("schedules:user:{$user->id}");

        // Minggu 3: umumkan event, biar Listener yang urus reaksinya
        ScheduleWasCreated::dispatch($schedule);

        return $schedule;
    }

    public function show(User $user, Schedule $schedule): Schedule
    {
        $this->authorizeOwnership($user, $schedule);
        $schedule->load('subject');
        return $schedule;
    }

    public function update(User $user, Schedule $schedule, array $data): Schedule
    {
        $this->authorizeOwnership($user, $schedule);
        $updated = $this->scheduleRepository->update($schedule, $data);
        Cache::forget("schedules:user:{$user->id}");
        return $updated;
    }

    public function delete(User $user, Schedule $schedule): void
    {
        $this->authorizeOwnership($user, $schedule);
        $this->scheduleRepository->delete($schedule);
        Cache::forget("schedules:user:{$user->id}");
    }

    protected function authorizeOwnership(User $user, Schedule $schedule): void
    {
        if ($schedule->user_id !== $user->id) {
            abort(403, 'Tidak diizinkan');
        }
    }
}