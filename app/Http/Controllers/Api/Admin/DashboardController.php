<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\SalonService;
use App\Models\Staff;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return response()->json([
            'total_services' => SalonService::count(),
            'active_staff' => Staff::where('is_active', true)->count(),
            'pending_appointments' => Appointment::where('status', 'pending')->count(),
            'confirmed_appointments' => Appointment::where('status', 'confirmed')->count(),
            'today_appointments' => Appointment::whereDate('appointment_date', now()->toDateString())->count(),
        ]);
    }
}
