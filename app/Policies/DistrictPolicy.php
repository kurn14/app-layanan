<?php

namespace App\Policies;

use App\Enums\PermissionType;
use App\Models\District;
use App\Models\User;
use App\Policies\Concerns\HandlesBulkPermissions;

class DistrictPolicy
{
    use HandlesBulkPermissions;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageRegion);
    }

    public function view(User $user, District $district): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageRegion);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageRegion)
            && ! $user->hasRole('pimpinan');
    }

    public function update(User $user, District $district): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageRegion)
            && ! $user->hasRole('pimpinan');
    }

    public function delete(User $user, District $district): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageRegion)
            && ! $user->hasRole('pimpinan');
    }

    public function restore(User $user, District $district): bool
    {
        return $this->update($user, $district);
    }

    public function forceDelete(User $user, District $district): bool
    {
        return $user->hasRole('administrator');
    }
}
