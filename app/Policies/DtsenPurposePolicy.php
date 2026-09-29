<?php

namespace App\Policies;

use App\Enums\PermissionType;
use App\Models\DtsenPurpose;
use App\Models\User;
use App\Policies\Concerns\HandlesBulkPermissions;

class DtsenPurposePolicy
{
    use HandlesBulkPermissions;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageDtsenPurpose);
    }

    public function view(User $user, DtsenPurpose $purpose): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageDtsenPurpose);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageDtsenPurpose)
            && ! $user->hasRole('pimpinan');
    }

    public function update(User $user, DtsenPurpose $purpose): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageDtsenPurpose)
            && ! $user->hasRole('pimpinan');
    }

    public function delete(User $user, DtsenPurpose $purpose): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageDtsenPurpose)
            && ! $user->hasRole('pimpinan');
    }

    public function restore(User $user, DtsenPurpose $purpose): bool
    {
        return $this->update($user, $purpose);
    }

    public function forceDelete(User $user, DtsenPurpose $purpose): bool
    {
        return $user->hasRole('administrator');
    }
}
