<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\PublicServiceResource;
use App\Models\SalonService;

class ServiceController extends Controller
{
    public function index()
    {
        $query = SalonService::query()
            ->with(['category', 'staff'])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name');

        if (request('audience')) {
            $query->whereIn('audience', ['all', request('audience')]);
        }

        if (request('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', request('category')));
        }

        return PublicServiceResource::collection($query->get());
    }

    public function show(string $slug)
    {
        $service = SalonService::query()
            ->with(['category', 'staff'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return new PublicServiceResource($service);
    }
}
