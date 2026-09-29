<?php

namespace App\Policies;

use App\Enums\PermissionType;
use App\Models\DownloadableForm;
use App\Models\User;
use App\Policies\Concerns\HandlesBulkPermissions;

class DownloadableFormPolicy
{
    use HandlesBulkPermissions;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageInformation);
    }

    public function view(User $user, DownloadableForm $form): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageInformation);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageInformation)
            && ! $user->hasRole('pimpinan');
    }

    public function update(User $user, DownloadableForm $form): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageInformation)
            && ! $user->hasRole('pimpinan');
    }

    public function delete(User $user, DownloadableForm $form): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageInformation)
            && ! $user->hasRole('pimpinan');
    }

    public function restore(User $user, DownloadableForm $form): bool
    {
        return $this->update($user, $form);
    }

    public function forceDelete(User $user, DownloadableForm $form): bool
    {
        return $user->hasRole('administrator');
    }
}
