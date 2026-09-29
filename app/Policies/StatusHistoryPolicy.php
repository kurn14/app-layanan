<?php

namespace App\Policies;

use App\Enums\PermissionType;
use App\Models\StatusHistory;
use App\Models\User;
use App\Policies\Concerns\HandlesBulkPermissions;

class StatusHistoryPolicy
{
    use HandlesBulkPermissions;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageServiceRequest)
            || $user->hasPermissionTo(PermissionType::ManageComplaint);
    }

    public function view(User $user, StatusHistory $history): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user) && ! $user->hasRole('pimpinan');
    }

    public function update(User $user, StatusHistory $history): bool
    {
        return $this->viewAny($user) && ! $user->hasRole('pimpinan');
    }

    public function delete(User $user, StatusHistory $history): bool
    {
        return $this->viewAny($user) && ! $user->hasRole('pimpinan');
    }

    public function restore(User $user, StatusHistory $history): bool
    {
        return $this->update($user, $history);
    }

    public function forceDelete(User $user, StatusHistory $history): bool
    {
        return $user->hasRole('administrator');
    }
}
