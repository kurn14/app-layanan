<?php

namespace App\Policies;

use App\Enums\PermissionType;
use App\Models\ServiceType;
use App\Models\User;
use App\Policies\Concerns\HandlesBulkPermissions;

class ServiceTypePolicy
{
    use HandlesBulkPermissions;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageServiceType);
    }

    public function view(User $user, ServiceType $serviceType): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageServiceType);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageServiceType)
            && ! $user->hasRole('pimpinan');
    }

    public function update(User $user, ServiceType $serviceType): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageServiceType)
            && ! $user->hasRole('pimpinan');
    }

    public function delete(User $user, ServiceType $serviceType): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageServiceType)
            && ! $user->hasRole('pimpinan');
    }

    public function restore(User $user, ServiceType $serviceType): bool
    {
        return $this->update($user, $serviceType);
    }

    public function forceDelete(User $user, ServiceType $serviceType): bool
    {
        return $user->hasRole('administrator');
    }
}
