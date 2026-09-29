<?php

namespace App\Policies;

use App\Enums\PermissionType;
use App\Models\Disposition;
use App\Models\User;
use App\Policies\Concerns\HandlesBulkPermissions;

class DispositionPolicy
{
    use HandlesBulkPermissions;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageServiceRequest)
            || $user->hasPermissionTo(PermissionType::ManageComplaint);
    }

    public function view(User $user, Disposition $disposition): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user) && ! $user->hasRole('pimpinan');
    }

    public function update(User $user, Disposition $disposition): bool
    {
        return $this->viewAny($user) && ! $user->hasRole('pimpinan');
    }

    public function delete(User $user, Disposition $disposition): bool
    {
        return $this->viewAny($user) && ! $user->hasRole('pimpinan');
    }

    public function restore(User $user, Disposition $disposition): bool
    {
        return $this->update($user, $disposition);
    }

    public function forceDelete(User $user, Disposition $disposition): bool
    {
        return $user->hasRole('administrator');
    }
}
