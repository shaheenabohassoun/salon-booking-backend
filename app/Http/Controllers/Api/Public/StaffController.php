<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\PublicStaffResource;
use App\Models\Staff;

class StaffController extends Controller
{
    public function index()
    {
        $query = Staff::query()
            ->with('services')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('full_name');

        if (request('service_id')) {
            $query->whereHas('services', fn ($q) => $q->where('salon_services.id', request('service_id')));
        }

        return PublicStaffResource::collection($query->get());
    }

    public function show(string $slug)
    {
        $staff = Staff::query()
            ->with('services')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return new PublicStaffResource($staff);
    }
}
