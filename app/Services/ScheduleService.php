<?php

namespace App\Services;

use App\Models\Schedule;
use App\Models\User;
use App\Repositories\ScheduleRepository;
use Illuminate\Database\Eloquent\Collection;

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
        return $this->scheduleRepository->create($user, $data);
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
        return $this->scheduleRepository->update($schedule, $data);
    }

    public function delete(User $user, Schedule $schedule): void
    {
        $this->authorizeOwnership($user, $schedule);
        $this->scheduleRepository->delete($schedule);
    }

    protected function authorizeOwnership(User $user, Schedule $schedule): void
    {
        if ($schedule->user_id !== $user->id) {
            abort(403, 'Tidak diizinkan');
        }
    }
}