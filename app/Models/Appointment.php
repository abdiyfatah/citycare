<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'appointment_date',
        'appointment_slot',
        'status',
        'reason',
        'consultation_notes',
        'diagnosis',
        'prescription',
    ];

    protected $casts = [
        'appointment_date' => 'date',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function patient(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function payment(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Payment::class);
    }

    // ── Scopes ─────────────────────────────────────────────────────────────────

    public function scopeToday($query)
    {
        return $query->whereDate('appointment_date', today());
    }

    public function scopeUpcoming($query)
    {
        return $query->whereDate('appointment_date', '>=', today())
                     ->whereIn('status', ['pending', 'confirmed']);
    }

    public function scopeForDoctor($query, int $doctorId)
    {
        return $query->where('doctor_id', $doctorId);
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'confirmed'  => 'badge-confirmed',
            'completed'  => 'badge-completed',
            'cancelled'  => 'badge-cancelled',
            'no_show'    => 'badge-no-show',
            default      => 'badge-pending',
        };
    }

    public function isEditable(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }
}
