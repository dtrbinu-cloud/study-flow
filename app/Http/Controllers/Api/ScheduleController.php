<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreScheduleRequest;
use App\Http\Requests\UpdateScheduleRequest;
use App\Http\Resources\ScheduleResource;
use App\Models\Schedule;
use App\Services\ScheduleService;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function __construct(
        protected ScheduleService $scheduleService
    ) {}

    // GET /api/schedules
    public function index(Request $request)
    {
        $schedules = $this->scheduleService->getAll($request->user());

        return response()->json([
            'data' => ScheduleResource::collection($schedules),
        ]);
    }

    // POST /api/schedules
    public function store(StoreScheduleRequest $request)
    {
        $schedule = $this->scheduleService->create($request->user(), $request->validated());

        return response()->json([
            'message' => 'Jadwal belajar berhasil dibuat',
            'data' => new ScheduleResource($schedule),
        ], 201);
    }

    // GET /api/schedules/{id}
    public function show(Request $request, Schedule $schedule)
    {
        $schedule = $this->scheduleService->show($request->user(), $schedule);

        return response()->json([
            'data' => new ScheduleResource($schedule),
        ]);
    }

    // PUT /api/schedules/{id}
    public function update(UpdateScheduleRequest $request, Schedule $schedule)
    {
        $schedule = $this->scheduleService->update($request->user(), $schedule, $request->validated());

        return response()->json([
            'message' => 'Jadwal belajar berhasil diperbarui',
            'data' => new ScheduleResource($schedule),
        ]);
    }

    // DELETE /api/schedules/{id}
    public function destroy(Request $request, Schedule $schedule)
    {
        $this->scheduleService->delete($request->user(), $schedule);

        return response()->json([
            'message' => 'Jadwal belajar berhasil dihapus',
        ]);
    }
}
