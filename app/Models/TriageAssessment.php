<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TriageAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'queue_entry_id',
        'assessed_by',
        'category',
        'systolic_bp',
        'diastolic_bp',
        'heart_rate',
        'respiratory_rate',
        'temperature',
        'oxygen_saturation',
        'consciousness_level',
        'chief_complaint',
        'observations',
        'is_immediate_response_triggered',
        'assessed_at',
    ];

    protected $casts = [
        'systolic_bp' => 'integer',
        'diastolic_bp' => 'integer',
        'heart_rate' => 'integer',
        'respiratory_rate' => 'integer',
        'temperature' => 'float',
        'oxygen_saturation' => 'integer',
        'is_immediate_response_triggered' => 'boolean',
        'assessed_at' => 'datetime',
    ];

    public function queueEntry(): BelongsTo
    {
        return $this->belongsTo(QueueEntry::class);
    }

    public function assessedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assessed_by');
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'merah' => 'Merah (Resusitasi/Gawat Darurat)',
            'kuning' => 'Kuning (Mendesak/Urgent)',
            'hijau' => 'Hijau (Tidak Mendesak)',
            'hitam' => 'Hitam (Meninggal)',
            default => ucfirst($this->category),
        };
    }
}
