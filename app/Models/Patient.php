<?php

namespace App\Models;

use App\Helpers\PhoneHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nik',
        'name',
        'date_of_birth',
        'gender',
        'phone',
        'address',
        'bpjs_number',
        'patient_tier',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function queueEntries(): HasMany
    {
        return $this->hasMany(QueueEntry::class);
    }

    public function teleconsultations(): HasMany
    {
        return $this->hasMany(Teleconsultation::class);
    }

    public function getAgeAttribute(): int
    {
        return $this->date_of_birth ? $this->date_of_birth->age : 0;
    }

    /**
     * Return masked NIK: show first 6 and last 4 digits only.
     */
    public function getMaskedNikAttribute(): string
    {
        if (strlen($this->nik) <= 10) {
            return $this->nik;
        }

        $first = substr($this->nik, 0, 6);
        $last = substr($this->nik, -4);
        $masked = str_repeat('*', strlen($this->nik) - 10);

        return $first.$masked.$last;
    }

    /**
     * Normalize gender to 'L' or 'P'.
     */
    public function setGenderAttribute(?string $value): void
    {
        if (in_array(strtolower((string) $value), ['male', 'l', 'laki-laki'])) {
            $this->attributes['gender'] = 'L';
        } elseif (in_array(strtolower((string) $value), ['female', 'p', 'perempuan'])) {
            $this->attributes['gender'] = 'P';
        } else {
            $this->attributes['gender'] = $value;
        }
    }

    /**
     * Human-friendly gender label.
     */
    public function getGenderLabelAttribute(): string
    {
        return in_array($this->gender, ['L', 'male']) ? 'Laki-laki' : 'Perempuan';
    }

    /**
     * Normalize phone number before saving.
     */
    public function setPhoneAttribute(?string $value): void
    {
        $this->attributes['phone'] = PhoneHelper::normalize($value);
    }

    /**
     * Nomor Peserta (murni angka).
     */
    public function getMemberNumberAttribute(): string
    {
        // 3276 (Kode Kota Depok) diikuti 8 digit ID pasien
        return '3276'.str_pad((string) $this->id, 8, '0', STR_PAD_LEFT);
    }

    /**
     * Kunci Unik Pasien (Security Key).
     */
    public function getUniquePatientKeyAttribute(): string
    {
        return 'PKM-DPK-'.strtoupper(substr(md5('pkm_depok_'.$this->id.'_'.$this->nik), 0, 8));
    }

    /**
     * Whether patient is in the subsidized tier.
     */
    public function getIsSubsidizedAttribute(): bool
    {
        if ($this->patient_tier === 'mandiri' && empty($this->bpjs_number)) {
            return false;
        }

        return ! empty($this->bpjs_number) || $this->patient_tier === 'subsidi';
    }

    /**
     * Status Golongan Layanan Pasien.
     */
    public function getServiceTierAttribute(): string
    {
        return $this->is_subsidized
            ? 'Subsidi Penuh (Bebas Biaya Rp 0)'
            : 'Mandiri / Reguler';
    }
}
