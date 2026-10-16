<?php

namespace App\Policies;

use App\Models\Teleconsultation;
use App\Models\User;

class TeleconsultationPolicy
{
    /**
     * Determine whether the user can view the teleconsultation details.
     */
    public function view(User $user, Teleconsultation $teleconsultation): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isPatient()) {
            return $user->patient && $user->patient->id === $teleconsultation->patient_id;
        }

        if ($user->isDoctor()) {
            return $this->isDoctorAuthorized($user, $teleconsultation);
        }

        return false;
    }

    /**
     * Determine whether the patient can access the waiting/preparation room.
     */
    public function accessWaitingRoom(User $user, Teleconsultation $teleconsultation): bool
    {
        if (! $user->isPatient() || ! $user->patient || $user->patient->id !== $teleconsultation->patient_id) {
            return false;
        }

        return $teleconsultation->canPatientAccessWaiting();
    }

    /**
     * Determine whether the patient can enter the active video room.
     */
    public function joinAsPatient(User $user, Teleconsultation $teleconsultation): bool
    {
        if (! $user->isPatient() || ! $user->patient || $user->patient->id !== $teleconsultation->patient_id) {
            return false;
        }

        if ($teleconsultation->isCancelled() || $teleconsultation->isCompleted()) {
            return false;
        }

        // Must have consented and doctor is ready / in progress
        return $teleconsultation->patient_consent_given && in_array($teleconsultation->status, ['doctor_ready', 'in_progress']);
    }

    /**
     * Determine whether the doctor can join the video conference room.
     */
    public function joinAsDoctor(User $user, Teleconsultation $teleconsultation): bool
    {
        if (! $user->isDoctor() && ! $user->isAdmin()) {
            return false;
        }

        if ($teleconsultation->isCancelled() || $teleconsultation->isCompleted()) {
            return false;
        }

        return $this->isDoctorAuthorized($user, $teleconsultation);
    }

    /**
     * Determine whether the doctor can start the session.
     */
    public function start(User $user, Teleconsultation $teleconsultation): bool
    {
        if (! $user->isDoctor() && ! $user->isAdmin()) {
            return false;
        }

        if ($teleconsultation->isCancelled() || $teleconsultation->isCompleted()) {
            return false;
        }

        return $this->isDoctorAuthorized($user, $teleconsultation);
    }

    /**
     * Determine whether the doctor can end and complete the session.
     */
    public function complete(User $user, Teleconsultation $teleconsultation): bool
    {
        if (! $user->isDoctor() && ! $user->isAdmin()) {
            return false;
        }

        if ($teleconsultation->isCancelled() || $teleconsultation->isCompleted()) {
            return false;
        }

        return $this->isDoctorAuthorized($user, $teleconsultation);
    }

    /**
     * Check if doctor is authorized for this teleconsultation session.
     */
    protected function isDoctorAuthorized(User $user, Teleconsultation $teleconsultation): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // If assigned to this specific doctor
        if ($teleconsultation->doctor_id && $teleconsultation->doctor_id === $user->id) {
            return true;
        }

        // If not assigned or assigned to same doctor, and doctor has schedule or belongs to this service
        return $user->staffSchedules()->where('service_id', $teleconsultation->service_id)->exists();
    }
}
