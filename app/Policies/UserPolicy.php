<?php

namespace App\Policies;

use App\Enums\PermissionType;
use App\Models\User;
use App\Policies\Concerns\HandlesBulkPermissions;

class UserPolicy
{
    use HandlesBulkPermissions;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageUser);
    }

    public function view(User $user, User $targetUser): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageUser);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageUser)
            && ! $user->hasRole('pimpinan');
    }

    public function update(User $user, User $targetUser): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageUser)
            && ! $user->hasRole('pimpinan');
    }

    public function delete(User $user, User $targetUser): bool
    {
        // Pengguna tidak boleh menghapus akunnya sendiri
        if ($targetUser->id === $user->id) {
            return false;
        }

        return $user->hasPermissionTo(PermissionType::ManageUser)
            && ! $user->hasRole('pimpinan');
    }

    public function restore(User $user, User $targetUser): bool
    {
        return $this->update($user, $targetUser);
    }

    public function forceDelete(User $user, User $targetUser): bool
    {
        if ($targetUser->id === $user->id) {
            return false;
        }

        return $user->hasRole('administrator');
    }
}
