<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'patient_number',
        'date_of_birth',
        'gender',
        'phone',
        'emergency_contact_name',
        'emergency_contact_phone',
        'address',
        'blood_group',
        'allergies',
        'medical_notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function appointments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth?->age;
    }

    public function getNameAttribute(): string
    {
        return $this->user->name ?? '';
    }

    /**
     * Generate a unique patient number like CC-2024-0001.
     */
    public static function generatePatientNumber(): string
    {
        $year = date('Y');
        $last = static::whereYear('created_at', $year)->max('id') ?? 0;
        return 'CC-' . $year . '-' . str_pad($last + 1, 4, '0', STR_PAD_LEFT);
    }
}
