<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGuestAppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Models\AppointmentStatusLog;
use App\Models\Customer;
use App\Models\SalonService;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function store(StoreGuestAppointmentRequest $request)
    {
        $service = SalonService::query()->with('staff')->findOrFail($request->integer('service_id'));
        $staff = $request->filled('staff_id')
            ? Staff::query()->where('is_active', true)->findOrFail($request->integer('staff_id'))
            : $service->staff()->where('is_active', true)->orderBy('sort_order')->first();

        abort_unless($staff, 422, 'لا يوجد موظف متاح لهذه الخدمة.');

        $date = Carbon::parse($request->string('appointment_date')->value());
        $start = Carbon::createFromFormat('Y-m-d H:i', $date->format('Y-m-d').' '.$request->string('start_time')->value());
        $end = (clone $start)->addMinutes($service->duration_minutes);

        $conflict = Appointment::query()
            ->where('staff_id', $staff->id)
            ->whereDate('appointment_date', $date->toDateString())
            ->whereIn('status', ['pending', 'confirmed'])
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('start_time', [$start->format('H:i:s'), $end->format('H:i:s')])
                    ->orWhereBetween('end_time', [$start->format('H:i:s'), $end->format('H:i:s')])
                    ->orWhere(function ($inner) use ($start, $end) {
                        $inner->where('start_time', '<=', $start->format('H:i:s'))
                            ->where('end_time', '>=', $end->format('H:i:s'));
                    });
            })
            ->exists();

        abort_if($conflict, 422, 'هذا الموعد محجوز بالفعل. اختر وقتاً آخر.');

        $customer = Customer::query()->firstOrCreate(
            ['phone' => $request->string('phone')->value()],
            [
                'full_name' => $request->string('full_name')->value(),
                'email' => $request->string('email')->value() ?: null,
                'notes' => $request->string('notes')->value() ?: null,
                'is_guest' => true,
            ],
        );

        $appointment = Appointment::query()->create([
            'customer_id' => $customer->id,
            'salon_service_id' => $service->id,
            'staff_id' => $staff->id,
            'appointment_date' => $date->toDateString(),
            'start_time' => $start->format('H:i:s'),
            'end_time' => $end->format('H:i:s'),
            'status' => 'pending',
            'booking_reference' => 'SB-'.Str::upper(Str::random(8)),
            'customer_name' => $request->string('full_name')->value(),
            'customer_phone' => $request->string('phone')->value(),
            'customer_email' => $request->string('email')->value() ?: null,
            'notes' => $request->string('notes')->value() ?: null,
            'price_snapshot' => $service->price,
            'duration_minutes' => $service->duration_minutes,
        ]);

        AppointmentStatusLog::query()->create([
            'appointment_id' => $appointment->id,
            'to_status' => 'pending',
            'note' => 'Booking created by guest.',
        ]);

        return new AppointmentResource(
            $appointment->load(['service', 'staff', 'customer'])
        );
    }

    public function availability()
    {
        $service = SalonService::query()->findOrFail(request()->integer('service_id'));
        $staff = request()->filled('staff_id')
            ? Staff::query()->findOrFail(request()->integer('staff_id'))
            : $service->staff()->where('is_active', true)->orderBy('sort_order')->firstOrFail();

        $date = Carbon::parse((string) request('date', now()->toDateString()));
        $slots = [];

        for ($hour = 10; $hour < 20; $hour++) {
            foreach ([0, 30] as $minute) {
                $start = Carbon::create($date->year, $date->month, $date->day, $hour, $minute);
                $end = (clone $start)->addMinutes($service->duration_minutes);

                $conflict = Appointment::query()
                    ->where('staff_id', $staff->id)
                    ->whereDate('appointment_date', $date->toDateString())
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->where(function ($query) use ($start, $end) {
                        $query->whereBetween('start_time', [$start->format('H:i:s'), $end->format('H:i:s')])
                            ->orWhereBetween('end_time', [$start->format('H:i:s'), $end->format('H:i:s')])
                            ->orWhere(function ($inner) use ($start, $end) {
                                $inner->where('start_time', '<=', $start->format('H:i:s'))
                                    ->where('end_time', '>=', $end->format('H:i:s'));
                            });
                    })
                    ->exists();

                if (! $conflict && $start->isFuture()) {
                    $slots[] = $start->format('H:i');
                }
            }
        }

        return response()->json([
            'date' => $date->toDateString(),
            'staff_id' => $staff->id,
            'slots' => $slots,
        ]);
    }
}
