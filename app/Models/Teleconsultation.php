<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Teleconsultation extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'patient_id',
        'service_id',
        'doctor_id',
        'session_id',
        'room_name',
        'status',
        'scheduled_at',
        'started_at',
        'ended_at',
        'duration_minutes',
        'doctor_joined_at',
        'patient_joined_at',
        'patient_consent_given',
        'patient_consent_at',
        'completed_by',
        'clinical_notes',
        'disposition',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
            'doctor_joined_at' => 'datetime',
            'patient_joined_at' => 'datetime',
            'patient_consent_given' => 'boolean',
            'patient_consent_at' => 'datetime',
            'duration_minutes' => 'integer',
        ];
    }

    /**
     * Generate an unguessable, cryptographically secure room name.
     * Never exposes patient name, NIK, phone number, diagnosis, or sequential ID.
     */
    public static function generateSecureRoomName(): string
    {
        $prefix = (string) config('jitsi.room_prefix', 'pkflow_');
        $random = bin2hex(random_bytes(16)); // 32 characters hex

        return $prefix.$random;
    }

    /**
     * Generate unique session identifier.
     */
    public static function generateSessionId(): string
    {
        return 'tele_'.(string) Str::uuid();
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function completedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function isScheduled(): bool
    {
        return $this->status === 'scheduled';
    }

    public function isWaitingDoctor(): bool
    {
        return $this->status === 'waiting_doctor';
    }

    public function isDoctorReady(): bool
    {
        return $this->status === 'doctor_ready';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Can patient enter the live video room directly?
     * Patient can enter only when doctor is ready or session is in progress,
     * and consultation is neither cancelled nor completed.
     */
    public function canPatientEnterRoom(): bool
    {
        if ($this->isCancelled() || $this->isCompleted()) {
            return false;
        }

        return in_array($this->status, ['doctor_ready', 'in_progress']);
    }

    /**
     * Can patient view the waiting/preparation room?
     */
    public function canPatientAccessWaiting(): bool
    {
        return ! in_array($this->status, ['completed', 'cancelled']);
    }

    /**
     * Can doctor start or enter the consultation room?
     */
    public function canDoctorAccess(): bool
    {
        return ! in_array($this->status, ['completed', 'cancelled']);
    }

    /**
     * Check if consultation time is reached or within early join window (e.g. 15 minutes before).
     */
    public function isTimeReached(): bool
    {
        if (! $this->scheduled_at) {
            return true;
        }

        // Allow entering waiting room 15 minutes prior to scheduled slot
        return Carbon::now()->gte($this->scheduled_at->copy()->subMinutes(15));
    }

    /**
     * Human readable status label in Indonesian.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'scheduled' => 'Terjadwal',
            'waiting_doctor' => 'Menunggu Dokter',
            'doctor_ready' => 'Dokter Tersedia',
            'in_progress' => 'Sedang Berlangsung',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    /**
     * Tailwind badge styling classes.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'scheduled' => 'bg-slate-100 text-slate-700 border-slate-200',
            'waiting_doctor' => 'bg-amber-50 text-amber-700 border-amber-200',
            'doctor_ready' => 'bg-emerald-50 text-accent border-emerald-200 animate-pulse',
            'in_progress' => 'bg-sky-50 text-primary border-sky-200',
            'completed' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
            'cancelled' => 'bg-red-50 text-red-600 border-red-200',
            default => 'bg-slate-50 text-slate-600 border-slate-200',
        };
    }
}
