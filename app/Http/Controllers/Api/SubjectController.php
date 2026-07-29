<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\UpdateSubjectRequest;
use App\Http\Resources\SubjectResource;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    // GET /api/subjects
    public function index(Request $request)
    {
        // subject milik user yang login
        $subjects = $request->user()
            ->subjects()
            ->withCount('schedules')
            ->latest()
            ->get();

        return response()->json([
            'data' => SubjectResource::collection($subjects),
        ]);
    }

    // POST /api/subjects
    public function store(StoreSubjectRequest $request)
    {
        $subject = $request->user()->subjects()->create($request->validated());

        return response()->json([
            'message' => 'Mata pelajaran berhasil dibuat',
            'data' => new SubjectResource($subject),
        ], 201);
    }

    // PUT /api/subjects/{id}
    public function update(UpdateSubjectRequest $request, Subject $subject)
    {
        // subject ini milik user yang login
        if ($subject->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Tidak diizinkan'], 403);
        }

        $subject->update($request->validated());

        return response()->json([
            'message' => 'Mata pelajaran berhasil diperbarui',
            'data' => new SubjectResource($subject),
        ]);
    }

    // DELETE /api/subjects/{id}
    public function destroy(Request $request, Subject $subject)
    {
        if ($subject->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Tidak diizinkan'], 403);
        }

        $subject->delete();

        return response()->json([
            'message' => 'Mata pelajaran berhasil dihapus',
        ]);
    }
}
