<?php

namespace App\Services;

use App\Models\Subject;
use App\Models\User;
use App\Repositories\SubjectRepository;
use Illuminate\Database\Eloquent\Collection;

class SubjectService
{
    public function __construct(
        protected SubjectRepository $subjectRepository
    ) {}

    public function getAll(User $user): Collection
    {
        return $this->subjectRepository->getAllByUser($user);
    }

    public function create(User $user, array $data): Subject
    {
        return $this->subjectRepository->create($user, $data);
    }

    public function update(User $user, Subject $subject, array $data): Subject
    {
        $this->authorizeOwnership($user, $subject);
        return $this->subjectRepository->update($subject, $data);
    }

    public function delete(User $user, Subject $subject): void
    {
        $this->authorizeOwnership($user, $subject);
        $this->subjectRepository->delete($subject);
    }

    protected function authorizeOwnership(User $user, Subject $subject): void
    {
        if ($subject->user_id !== $user->id) {
            abort(403, 'Tidak diizinkan');
        }
    }
}