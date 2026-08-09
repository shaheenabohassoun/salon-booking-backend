<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\SalonService;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        return SalonService::query()
            ->with(['category', 'staff'])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function store(StoreServiceRequest $request)
    {
        $data = $request->validated();
        $service = SalonService::query()->create([
            ...$data,
            'slug' => $data['slug'] ?: Str::slug($data['name']),
        ]);
        $service->staff()->sync($data['staff_ids'] ?? []);

        return $service->load(['category', 'staff']);
    }

    public function show(SalonService $service)
    {
        return $service->load(['category', 'staff']);
    }

    public function update(UpdateServiceRequest $request, SalonService $service)
    {
        $data = $request->validated();
        $service->update([
            ...$data,
            'slug' => $data['slug'] ?: Str::slug($data['name']),
        ]);
        $service->staff()->sync($data['staff_ids'] ?? []);

        return $service->load(['category', 'staff']);
    }

    public function destroy(SalonService $service)
    {
        $service->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
