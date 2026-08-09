<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['staff_id', 'day_of_week', 'start_time', 'end_time', 'is_day_off'])]
class WorkingHour extends Model
{
    protected function casts(): array
    {
        return [
            'is_day_off' => 'boolean',
        ];
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }
}
