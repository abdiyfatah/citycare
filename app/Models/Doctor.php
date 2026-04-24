<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'department_id',
        'specialisation',
        'qualification',
        'phone',
        'bio',
        'schedule',
        'consultation_fee',
        'is_active',
    ];

    protected $casts = [
        'schedule'         => 'array',
        'consultation_fee' => 'decimal:2',
        'is_active'        => 'boolean',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function appointments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    /**
     * Return available slots for a given date, excluding already-booked ones.
     */
    public function availableSlotsForDate(string $date): array
    {
        $dayName = strtolower(date('l', strtotime($date)));
        $schedule = $this->schedule ?? [];
        $allSlots = $schedule[$dayName] ?? [];

        // Fetch booked (non-cancelled) slots for this date
        $bookedSlots = $this->appointments()
            ->where('appointment_date', $date)
            ->whereNotIn('status', ['cancelled'])
            ->pluck('appointment_slot')
            ->toArray();

        return array_values(array_diff($allSlots, $bookedSlots));
    }

    public function getNameAttribute(): string
    {
        return $this->user->name ?? '';
    }

    // ── Scopes ─────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
