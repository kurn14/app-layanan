<?php

namespace App\Policies;

use App\Enums\PermissionType;
use App\Models\PbiReactivation;
use App\Models\User;
use App\Policies\Concerns\HandlesBulkPermissions;

class PbiReactivationPolicy
{
    use HandlesBulkPermissions;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageServiceRequest)
            || $user->hasPermissionTo(PermissionType::ApprovePbiRecommendation);
    }

    public function view(User $user, PbiReactivation $reactivation): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageServiceRequest)
            || $user->hasPermissionTo(PermissionType::ApprovePbiRecommendation);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageServiceRequest)
            && ! $user->hasRole('pimpinan');
    }

    public function update(User $user, PbiReactivation $reactivation): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageServiceRequest)
            && ! $user->hasRole('pimpinan');
    }

    public function delete(User $user, PbiReactivation $reactivation): bool
    {
        return $this->update($user, $reactivation);
    }

    public function restore(User $user, PbiReactivation $reactivation): bool
    {
        return $this->update($user, $reactivation);
    }

    public function forceDelete(User $user, PbiReactivation $reactivation): bool
    {
        return $user->hasRole('administrator');
    }

    /**
     * Otorisasi khusus untuk menandatangani / memaraf rekomendasi PBI.
     */
    public function approve(User $user, PbiReactivation $reactivation): bool
    {
        if (! $user->hasPermissionTo(PermissionType::ApprovePbiRecommendation)) {
            return false;
        }

        return $reactivation->approvals()
            ->where('approver_id', $user->id)
            ->where('decision', 'pending')
            ->exists();
    }
}
