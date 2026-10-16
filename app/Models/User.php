<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isRegistrar(): bool
    {
        return $this->role === 'registrar';
    }

    public function isHealthOfficer(): bool
    {
        return $this->role === 'health_officer';
    }

    public function isDoctor(): bool
    {
        return $this->role === 'doctor';
    }

    public function isPatient(): bool
    {
        return $this->role === 'patient';
    }

    public function isStaff(): bool
    {
        return in_array($this->role, ['admin', 'registrar', 'health_officer', 'doctor']);
    }

    public function staffSchedules(): HasMany
    {
        return $this->hasMany(StaffSchedule::class);
    }

    public function triageAssessments(): HasMany
    {
        return $this->hasMany(TriageAssessment::class, 'assessed_by');
    }

    public function consultationSessions(): HasMany
    {
        return $this->hasMany(ConsultationSession::class, 'doctor_id');
    }

    public function doctorTeleconsultations(): HasMany
    {
        return $this->hasMany(Teleconsultation::class, 'doctor_id');
    }

    public function patient(): HasOne
    {
        return $this->hasOne(Patient::class);
    }
}
