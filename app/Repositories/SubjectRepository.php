<?php

namespace App\Repositories;

use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class SubjectRepository
{
    public function getAllByUser(User $user): Collection
    {
        $cacheKey = "subjects:user:{$user->id}";

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($user) {
            return $user->subjects()->withCount('schedules')->latest()->get();
        });
    }

    public function create(User $user, array $data): Subject
    {
        return $user->subjects()->create($data);
    }

    public function update(Subject $subject, array $data): Subject
    {
        $subject->update($data);
        return $subject;
    }

    public function delete(Subject $subject): void
    {
        $subject->delete();
    }
}