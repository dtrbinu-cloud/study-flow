<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreScheduleRequest;
use App\Http\Requests\UpdateScheduleRequest;
use App\Http\Resources\ScheduleResource;
use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    // GET /api/schedules
    public function index(Request $request)
    {
        $schedules = $request->user()
            ->schedules()
            ->with('subject')
            ->orderBy('study_date')
            ->orderBy('start_time')
            ->get();

        return response()->json([
            'data' => ScheduleResource::collection($schedules),
        ]);
    }

    // POST /api/schedules
    public function store(StoreScheduleRequest $request)
    {
        $schedule = $request->user()->schedules()->create($request->validated());
        $schedule->load('subject');

        return response()->json([
            'message' => 'Jadwal belajar berhasil dibuat',
            'data' => new ScheduleResource($schedule),
        ], 201);
    }

    // GET /api/schedules/{id}
    public function show(Request $request, Schedule $schedule)
    {
        if ($schedule->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Tidak diizinkan'], 403);
        }

        $schedule->load('subject');

        return response()->json([
            'data' => new ScheduleResource($schedule),
        ]);
    }

    // PUT /api/schedules/{id}
    public function update(UpdateScheduleRequest $request, Schedule $schedule)
    {
        if ($schedule->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Tidak diizinkan'], 403);
        }

        $schedule->update($request->validated());
        $schedule->load('subject');

        return response()->json([
            'message' => 'Jadwal belajar berhasil diperbarui',
            'data' => new ScheduleResource($schedule),
        ]);
    }

    // DELETE /api/schedules/{id}
    public function destroy(Request $request, Schedule $schedule)
    {
        if ($schedule->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Tidak diizinkan'], 403);
        }

        $schedule->delete();

        return response()->json([
            'message' => 'Jadwal belajar berhasil dihapus',
        ]);
    }
}
