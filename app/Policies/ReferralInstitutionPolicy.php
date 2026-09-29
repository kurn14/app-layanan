<?php

namespace App\Policies;

use App\Enums\PermissionType;
use App\Models\ReferralInstitution;
use App\Models\User;
use App\Policies\Concerns\HandlesBulkPermissions;

class ReferralInstitutionPolicy
{
    use HandlesBulkPermissions;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageReferralInstitution);
    }

    public function view(User $user, ReferralInstitution $institution): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageReferralInstitution);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageReferralInstitution)
            && ! $user->hasRole('pimpinan');
    }

    public function update(User $user, ReferralInstitution $institution): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageReferralInstitution)
            && ! $user->hasRole('pimpinan');
    }

    public function delete(User $user, ReferralInstitution $institution): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageReferralInstitution)
            && ! $user->hasRole('pimpinan');
    }

    public function restore(User $user, ReferralInstitution $institution): bool
    {
        return $this->update($user, $institution);
    }

    public function forceDelete(User $user, ReferralInstitution $institution): bool
    {
        return $user->hasRole('administrator');
    }
}
