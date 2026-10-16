<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class QueueEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'queue_code',
        'service_id',
        'patient_id',
        'appointment_id',
        'queue_date',
        'registration_type',
        'priority_level',
        'status',
        'estimated_call_time',
        'called_at',
        'consultation_started_at',
        'completed_at',
        'current_counter',
    ];

    protected $casts = [
        'queue_date' => 'date',
        'priority_level' => 'integer',
        'estimated_call_time' => 'datetime',
        'called_at' => 'datetime',
        'consultation_started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function triageAssessment(): HasOne
    {
        return $this->hasOne(TriageAssessment::class);
    }

    public function consultationSession(): HasOne
    {
        return $this->hasOne(ConsultationSession::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(QueueStatusHistory::class)->orderBy('created_at', 'asc');
    }

    public function getPriorityLabelAttribute(): string
    {
        return match ($this->priority_level) {
            1 => 'Emergency (Merah)',
            2 => 'Urgent (Kuning)',
            3 => 'Normal (Hijau)',
            default => 'Normal',
        };
    }

    public function getPriorityBadgeColorAttribute(): string
    {
        return match ($this->priority_level) {
            1 => 'bg-red-500 text-white',
            2 => 'bg-amber-500 text-white',
            3 => 'bg-emerald-600 text-white',
            default => 'bg-gray-500 text-white',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'waiting_triage' => 'Menunggu Triase',
            'waiting_call' => 'Menunggu Panggilan',
            'called' => 'Dipanggil',
            'in_consultation' => 'Dalam Pelayanan',
            'completed' => 'Selesai',
            'skipped' => 'Dilewati',
            'cancelled' => 'Batal',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }
}
