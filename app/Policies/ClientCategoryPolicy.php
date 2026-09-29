<?php

namespace App\Policies;

use App\Enums\PermissionType;
use App\Models\ClientCategory;
use App\Models\User;
use App\Policies\Concerns\HandlesBulkPermissions;

class ClientCategoryPolicy
{
    use HandlesBulkPermissions;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageClientCategory);
    }

    public function view(User $user, ClientCategory $category): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageClientCategory);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageClientCategory)
            && ! $user->hasRole('pimpinan');
    }

    public function update(User $user, ClientCategory $category): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageClientCategory)
            && ! $user->hasRole('pimpinan');
    }

    public function delete(User $user, ClientCategory $category): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageClientCategory)
            && ! $user->hasRole('pimpinan');
    }

    public function restore(User $user, ClientCategory $category): bool
    {
        return $this->update($user, $category);
    }

    public function forceDelete(User $user, ClientCategory $category): bool
    {
        return $user->hasRole('administrator');
    }
}
