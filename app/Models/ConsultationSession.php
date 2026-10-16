<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsultationSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'queue_entry_id',
        'doctor_id',
        'service_id',
        'started_at',
        'ended_at',
        'duration_minutes',
        'clinical_notes',
        'disposition',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'duration_minutes' => 'integer',
    ];

    public function queueEntry(): BelongsTo
    {
        return $this->belongsTo(QueueEntry::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function getDispositionLabelAttribute(): string
    {
        return match ($this->disposition) {
            'pulang' => 'Pulang / Selesai Pengobatan',
            'rujuk_lanjut' => 'Rujuk Lanjut (RS / Spesialis)',
            'rawat_inap' => 'Rawat Inap Puskesmas',
            'kontrol_ulang' => 'Jadwal Kontrol Ulang',
            default => ucfirst(str_replace('_', ' ', $this->disposition)),
        };
    }
}
