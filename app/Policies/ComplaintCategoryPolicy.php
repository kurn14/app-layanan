<?php

namespace App\Policies;

use App\Enums\PermissionType;
use App\Models\ComplaintCategory;
use App\Models\User;
use App\Policies\Concerns\HandlesBulkPermissions;

class ComplaintCategoryPolicy
{
    use HandlesBulkPermissions;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageComplaintCategory);
    }

    public function view(User $user, ComplaintCategory $category): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageComplaintCategory);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageComplaintCategory)
            && ! $user->hasRole('pimpinan');
    }

    public function update(User $user, ComplaintCategory $category): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageComplaintCategory)
            && ! $user->hasRole('pimpinan');
    }

    public function delete(User $user, ComplaintCategory $category): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageComplaintCategory)
            && ! $user->hasRole('pimpinan');
    }

    public function restore(User $user, ComplaintCategory $category): bool
    {
        return $this->update($user, $category);
    }

    public function forceDelete(User $user, ComplaintCategory $category): bool
    {
        return $user->hasRole('administrator');
    }
}
