<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name',
    'email',
    'password',
    'phone',
    'nik',
    'work_unit_id',
    'district_id',
    'village_id',
    'is_active',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    public function canAccessPanel(Panel $panel): bool
    {
        return (bool) ($this->is_active ?? true);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function workUnit(): BelongsTo
    {
        return $this->belongsTo(WorkUnit::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    public function submittedServiceRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class, 'submitter_id');
    }

    public function handledServiceRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class, 'officer_id');
    }

    public function checkedDtsenCertificates(): HasMany
    {
        return $this->hasMany(DtsenCertificate::class, 'checker_id');
    }

    public function signedDtsenCertificates(): HasMany
    {
        return $this->hasMany(DtsenCertificate::class, 'signer_id');
    }

    public function signedPbiReactivations(): HasMany
    {
        return $this->hasMany(PbiReactivation::class, 'signer_id');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(Approval::class, 'approver_id');
    }

    public function rehabilitationCases(): HasMany
    {
        return $this->hasMany(RehabilitationCase::class, 'officer_id');
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class, 'officer_id');
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(Referral::class, 'officer_id');
    }

    public function monitoringRecords(): HasMany
    {
        return $this->hasMany(MonitoringRecord::class, 'officer_id');
    }

    public function reportedComplaints(): HasMany
    {
        return $this->hasMany(Complaint::class, 'reporter_id');
    }

    public function handledComplaints(): HasMany
    {
        return $this->hasMany(Complaint::class, 'officer_id');
    }

    public function managedInformationPages(): HasMany
    {
        return $this->hasMany(InformationPage::class, 'manager_id');
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(StatusHistory::class, 'user_id');
    }

    public function sentDispositions(): HasMany
    {
        return $this->hasMany(Disposition::class, 'from_user_id');
    }

    public function receivedDispositions(): HasMany
    {
        return $this->hasMany(Disposition::class, 'to_user_id');
    }
}
