<?php

namespace App\Policies;

use App\Enums\PermissionType;
use App\Models\RehabilitationCase;
use App\Models\User;
use App\Policies\Concerns\HandlesBulkPermissions;

class RehabilitationCasePolicy
{
    use HandlesBulkPermissions;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageRehabilitationCase);
    }

    public function view(User $user, RehabilitationCase $case): bool
    {
        if (! $user->hasPermissionTo(PermissionType::ManageRehabilitationCase)) {
            return false;
        }

        if ($user->hasRole('administrator') || $user->hasRole('pimpinan')) {
            return true;
        }

        return $case->officer_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageRehabilitationCase)
            && ! $user->hasRole('pimpinan');
    }

    public function update(User $user, RehabilitationCase $case): bool
    {
        if ($user->hasRole('pimpinan')) {
            return false;
        }

        if (! $user->hasPermissionTo(PermissionType::ManageRehabilitationCase)) {
            return false;
        }

        if ($user->hasRole('administrator')) {
            return true;
        }

        return $case->officer_id === $user->id;
    }

    public function delete(User $user, RehabilitationCase $case): bool
    {
        return $this->update($user, $case);
    }

    public function restore(User $user, RehabilitationCase $case): bool
    {
        return $this->update($user, $case);
    }

    public function forceDelete(User $user, RehabilitationCase $case): bool
    {
        return $user->hasRole('administrator');
    }
}
