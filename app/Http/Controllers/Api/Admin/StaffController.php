<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStaffRequest;
use App\Http\Requests\UpdateStaffRequest;
use App\Models\Staff;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index()
    {
        return Staff::query()
            ->with('services')
            ->orderBy('sort_order')
            ->orderBy('full_name')
            ->get();
    }

    public function store(StoreStaffRequest $request)
    {
        $data = $request->validated();
        $staff = Staff::query()->create([
            ...$data,
            'slug' => $data['slug'] ?: Str::slug($data['full_name']),
        ]);
        $staff->services()->sync($data['service_ids'] ?? []);

        return $staff->load('services');
    }

    public function show(Staff $staff)
    {
        return $staff->load('services');
    }

    public function update(UpdateStaffRequest $request, Staff $staff)
    {
        $data = $request->validated();
        $staff->update([
            ...$data,
            'slug' => $data['slug'] ?: Str::slug($data['full_name']),
        ]);
        $staff->services()->sync($data['service_ids'] ?? []);

        return $staff->load('services');
    }

    public function destroy(Staff $staff)
    {
        $staff->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
