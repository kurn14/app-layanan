<?php

namespace App\Policies;

use App\Enums\PermissionType;
use App\Models\User;
use App\Models\Village;
use App\Policies\Concerns\HandlesBulkPermissions;

class VillagePolicy
{
    use HandlesBulkPermissions;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageRegion);
    }

    public function view(User $user, Village $village): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageRegion);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageRegion)
            && ! $user->hasRole('pimpinan');
    }

    public function update(User $user, Village $village): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageRegion)
            && ! $user->hasRole('pimpinan');
    }

    public function delete(User $user, Village $village): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageRegion)
            && ! $user->hasRole('pimpinan');
    }

    public function restore(User $user, Village $village): bool
    {
        return $this->update($user, $village);
    }

    public function forceDelete(User $user, Village $village): bool
    {
        return $user->hasRole('administrator');
    }
}
