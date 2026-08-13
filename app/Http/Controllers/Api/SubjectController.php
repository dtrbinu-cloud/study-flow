<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\UpdateSubjectRequest;
use App\Http\Resources\SubjectResource;
use App\Models\Subject;
use App\Services\SubjectService;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function __construct(
        protected SubjectService $subjectService
    ) {}

    // GET /api/subjects
    public function index(Request $request)
    {
        $subjects = $this->subjectService->getAll($request->user());

        return response()->json([
            'data' => SubjectResource::collection($subjects),
        ]);
    }

    // POST /api/subjects
    public function store(StoreSubjectRequest $request)
    {
        $subject = $this->subjectService->create($request->user(), $request->validated());

        return response()->json([
            'message' => 'Mata pelajaran berhasil dibuat',
            'data' => new SubjectResource($subject),
        ], 201);
    }

    // PUT /api/subjects/{id}
    public function update(UpdateSubjectRequest $request, Subject $subject)
    {
        $subject = $this->subjectService->update($request->user(), $subject, $request->validated());

        return response()->json([
            'message' => 'Mata pelajaran berhasil diperbarui',
            'data' => new SubjectResource($subject),
        ]);
    }

    // DELETE /api/subjects/{id}
    public function destroy(Request $request, Subject $subject)
    {
        $this->subjectService->delete($request->user(), $subject);

        return response()->json([
            'message' => 'Mata pelajaran berhasil dihapus',
        ]);
    }
}