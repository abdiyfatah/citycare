<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'receipt_number',
        'amount',
        'payment_method',
        'status',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'amount'  => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function appointment(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    public static function generateReceiptNumber(): string
    {
        $year = date('Y');
        $last = static::whereYear('created_at', $year)->max('id') ?? 0;
        return 'RCP-' . $year . '-' . str_pad($last + 1, 4, '0', STR_PAD_LEFT);
    }
}
