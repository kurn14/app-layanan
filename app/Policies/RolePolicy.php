<?php

namespace App\Policies;

use App\Enums\PermissionType;
use App\Models\User;
use App\Policies\Concerns\HandlesBulkPermissions;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    use HandlesBulkPermissions;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageRole);
    }

    public function view(User $user, Role $role): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageRole);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageRole)
            && ! $user->hasRole('pimpinan');
    }

    public function update(User $user, Role $role): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageRole)
            && ! $user->hasRole('pimpinan');
    }

    public function delete(User $user, Role $role): bool
    {
        // 3 role bawaan sistem tidak boleh dihapus
        if (in_array($role->name, ['administrator', 'operator', 'pimpinan'])) {
            return false;
        }

        return $user->hasPermissionTo(PermissionType::ManageRole)
            && ! $user->hasRole('pimpinan');
    }

    public function restore(User $user, Role $role): bool
    {
        return $this->update($user, $role);
    }

    public function forceDelete(User $user, Role $role): bool
    {
        if (in_array($role->name, ['administrator', 'operator', 'pimpinan'])) {
            return false;
        }

        return $user->hasRole('administrator');
    }
}
