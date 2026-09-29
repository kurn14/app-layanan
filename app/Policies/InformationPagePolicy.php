<?php

namespace App\Policies;

use App\Enums\PermissionType;
use App\Models\InformationPage;
use App\Models\User;
use App\Policies\Concerns\HandlesBulkPermissions;

class InformationPagePolicy
{
    use HandlesBulkPermissions;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageInformation);
    }

    public function view(User $user, InformationPage $page): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageInformation);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageInformation)
            && ! $user->hasRole('pimpinan');
    }

    public function update(User $user, InformationPage $page): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageInformation)
            && ! $user->hasRole('pimpinan');
    }

    public function delete(User $user, InformationPage $page): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageInformation)
            && ! $user->hasRole('pimpinan');
    }

    public function restore(User $user, InformationPage $page): bool
    {
        return $this->update($user, $page);
    }

    public function forceDelete(User $user, InformationPage $page): bool
    {
        return $user->hasRole('administrator');
    }
}
