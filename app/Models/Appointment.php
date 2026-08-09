<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'customer_id',
    'salon_service_id',
    'staff_id',
    'appointment_date',
    'start_time',
    'end_time',
    'status',
    'booking_reference',
    'customer_name',
    'customer_phone',
    'customer_email',
    'notes',
    'internal_notes',
    'price_snapshot',
    'duration_minutes',
    'confirmed_at',
    'completed_at',
    'cancelled_at',
])]
class Appointment extends Model
{
    protected function casts(): array
    {
        return [
            'appointment_date' => 'date',
            'price_snapshot' => 'decimal:2',
            'confirmed_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(SalonService::class, 'salon_service_id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(AppointmentStatusLog::class);
    }
}
