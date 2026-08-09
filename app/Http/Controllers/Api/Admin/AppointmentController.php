<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Models\AppointmentStatusLog;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index()
    {
        $query = Appointment::query()
            ->with(['customer', 'service', 'staff'])
            ->orderByDesc('appointment_date')
            ->orderByDesc('start_time');

        if (request('status')) {
            $query->where('status', request('status'));
        }

        return AppointmentResource::collection($query->get());
    }

    public function show(Appointment $appointment)
    {
        return new AppointmentResource($appointment->load(['customer', 'service', 'staff']));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,confirmed,completed,cancelled'],
            'internal_notes' => ['nullable', 'string'],
        ]);

        $fromStatus = $appointment->status;
        $appointment->status = $data['status'];
        $appointment->internal_notes = $data['internal_notes'] ?? $appointment->internal_notes;

        if ($data['status'] === 'confirmed' && ! $appointment->confirmed_at) {
            $appointment->confirmed_at = now();
        }

        if ($data['status'] === 'completed' && ! $appointment->completed_at) {
            $appointment->completed_at = now();
        }

        if ($data['status'] === 'cancelled' && ! $appointment->cancelled_at) {
            $appointment->cancelled_at = now();
        }

        $appointment->save();

        AppointmentStatusLog::query()->create([
            'appointment_id' => $appointment->id,
            'changed_by' => $request->user()?->id,
            'from_status' => $fromStatus,
            'to_status' => $appointment->status,
            'note' => $appointment->internal_notes,
        ]);

        return new AppointmentResource($appointment->load(['customer', 'service', 'staff']));
    }
}
