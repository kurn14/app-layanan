<?php

namespace App\Policies;

use App\Enums\PermissionType;
use App\Models\Faq;
use App\Models\User;
use App\Policies\Concerns\HandlesBulkPermissions;

class FaqPolicy
{
    use HandlesBulkPermissions;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageInformation);
    }

    public function view(User $user, Faq $faq): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageInformation);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageInformation)
            && ! $user->hasRole('pimpinan');
    }

    public function update(User $user, Faq $faq): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageInformation)
            && ! $user->hasRole('pimpinan');
    }

    public function delete(User $user, Faq $faq): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageInformation)
            && ! $user->hasRole('pimpinan');
    }

    public function restore(User $user, Faq $faq): bool
    {
        return $this->update($user, $faq);
    }

    public function forceDelete(User $user, Faq $faq): bool
    {
        return $user->hasRole('administrator');
    }
}
