<?php

namespace App\Policies;

use App\Enums\PermissionType;
use App\Models\DtsenCertificate;
use App\Models\User;
use App\Policies\Concerns\HandlesBulkPermissions;

class DtsenCertificatePolicy
{
    use HandlesBulkPermissions;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageServiceRequest)
            || $user->hasPermissionTo(PermissionType::ApproveDtsenLetter);
    }

    public function view(User $user, DtsenCertificate $certificate): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageServiceRequest)
            || $user->hasPermissionTo(PermissionType::ApproveDtsenLetter);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageServiceRequest)
            && ! $user->hasRole('pimpinan');
    }

    public function update(User $user, DtsenCertificate $certificate): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageServiceRequest)
            && ! $user->hasRole('pimpinan');
    }

    public function delete(User $user, DtsenCertificate $certificate): bool
    {
        return $this->update($user, $certificate);
    }

    public function restore(User $user, DtsenCertificate $certificate): bool
    {
        return $this->update($user, $certificate);
    }

    public function forceDelete(User $user, DtsenCertificate $certificate): bool
    {
        return $user->hasRole('administrator');
    }

    /**
     * Otorisasi khusus untuk menandatangani / memaraf SK DTSEN.
     */
    public function approve(User $user, DtsenCertificate $certificate): bool
    {
        if (! $user->hasPermissionTo(PermissionType::ApproveDtsenLetter)) {
            return false;
        }

        return $certificate->approvals()
            ->where('approver_id', $user->id)
            ->where('decision', 'pending')
            ->exists();
    }
}
