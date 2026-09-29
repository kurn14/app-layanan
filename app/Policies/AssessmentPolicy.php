<?php

namespace App\Policies;

use App\Enums\PermissionType;
use App\Models\Assessment;
use App\Models\User;
use App\Policies\Concerns\HandlesBulkPermissions;

class AssessmentPolicy
{
    use HandlesBulkPermissions;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageRehabilitationCase);
    }

    public function view(User $user, Assessment $assessment): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageRehabilitationCase);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageRehabilitationCase)
            && ! $user->hasRole('pimpinan');
    }

    public function update(User $user, Assessment $assessment): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageRehabilitationCase)
            && ! $user->hasRole('pimpinan');
    }

    public function delete(User $user, Assessment $assessment): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageRehabilitationCase)
            && ! $user->hasRole('pimpinan');
    }

    public function restore(User $user, Assessment $assessment): bool
    {
        return $this->update($user, $assessment);
    }

    public function forceDelete(User $user, Assessment $assessment): bool
    {
        return $user->hasRole('administrator');
    }
}
