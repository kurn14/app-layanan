<?php

namespace App\Policies;

use App\Enums\PermissionType;
use App\Models\ServiceRequirement;
use App\Models\User;
use App\Policies\Concerns\HandlesBulkPermissions;

class ServiceRequirementPolicy
{
    use HandlesBulkPermissions;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageServiceType);
    }

    public function view(User $user, ServiceRequirement $requirement): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageServiceType);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageServiceType)
            && ! $user->hasRole('pimpinan');
    }

    public function update(User $user, ServiceRequirement $requirement): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageServiceType)
            && ! $user->hasRole('pimpinan');
    }

    public function delete(User $user, ServiceRequirement $requirement): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageServiceType)
            && ! $user->hasRole('pimpinan');
    }

    public function restore(User $user, ServiceRequirement $requirement): bool
    {
        return $this->update($user, $requirement);
    }

    public function forceDelete(User $user, ServiceRequirement $requirement): bool
    {
        return $user->hasRole('administrator');
    }
}
