<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'slot_duration_minutes',
        'max_capacity_per_slot',
        'is_active',
        'supports_teleconsultation',
    ];

    protected $casts = [
        'slot_duration_minutes' => 'integer',
        'max_capacity_per_slot' => 'integer',
        'is_active' => 'boolean',
        'supports_teleconsultation' => 'boolean',
    ];

    public function staffSchedules(): HasMany
    {
        return $this->hasMany(StaffSchedule::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function queueEntries(): HasMany
    {
        return $this->hasMany(QueueEntry::class);
    }

    public function consultationSessions(): HasMany
    {
        return $this->hasMany(ConsultationSession::class);
    }

    public function teleconsultations(): HasMany
    {
        return $this->hasMany(Teleconsultation::class);
    }
}
