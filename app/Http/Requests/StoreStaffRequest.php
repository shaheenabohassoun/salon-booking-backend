<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreStaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:120'],
            'slug' => ['nullable', 'string', 'max:120', 'unique:staff,slug'],
            'specialty' => ['nullable', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:120'],
            'avatar_url' => ['nullable', 'url', 'max:500'],
            'bio' => ['nullable', 'string'],
            'experience_years' => ['nullable', 'integer', 'min:0', 'max:60'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'service_ids' => ['nullable', 'array'],
            'service_ids.*' => ['integer', 'exists:salon_services,id'],
        ];
    }
}
