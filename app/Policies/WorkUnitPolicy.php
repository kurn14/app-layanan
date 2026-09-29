<?php

namespace App\Policies;

use App\Enums\PermissionType;
use App\Models\User;
use App\Models\WorkUnit;
use App\Policies\Concerns\HandlesBulkPermissions;

class WorkUnitPolicy
{
    use HandlesBulkPermissions;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageWorkUnit);
    }

    public function view(User $user, WorkUnit $workUnit): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageWorkUnit);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageWorkUnit)
            && ! $user->hasRole('pimpinan');
    }

    public function update(User $user, WorkUnit $workUnit): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageWorkUnit)
            && ! $user->hasRole('pimpinan');
    }

    public function delete(User $user, WorkUnit $workUnit): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageWorkUnit)
            && ! $user->hasRole('pimpinan');
    }

    public function restore(User $user, WorkUnit $workUnit): bool
    {
        return $this->update($user, $workUnit);
    }

    public function forceDelete(User $user, WorkUnit $workUnit): bool
    {
        return $user->hasRole('administrator');
    }
}
