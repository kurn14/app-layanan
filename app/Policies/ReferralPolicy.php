<?php

namespace App\Policies;

use App\Enums\PermissionType;
use App\Models\Referral;
use App\Models\User;
use App\Policies\Concerns\HandlesBulkPermissions;

class ReferralPolicy
{
    use HandlesBulkPermissions;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageRehabilitationCase);
    }

    public function view(User $user, Referral $referral): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageRehabilitationCase);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageRehabilitationCase)
            && ! $user->hasRole('pimpinan');
    }

    public function update(User $user, Referral $referral): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageRehabilitationCase)
            && ! $user->hasRole('pimpinan');
    }

    public function delete(User $user, Referral $referral): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageRehabilitationCase)
            && ! $user->hasRole('pimpinan');
    }

    public function restore(User $user, Referral $referral): bool
    {
        return $this->update($user, $referral);
    }

    public function forceDelete(User $user, Referral $referral): bool
    {
        return $user->hasRole('administrator');
    }
}
