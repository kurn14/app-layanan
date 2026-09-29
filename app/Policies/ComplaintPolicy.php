<?php

namespace App\Policies;

use App\Enums\PermissionType;
use App\Models\Complaint;
use App\Models\User;
use App\Models\Village;
use App\Policies\Concerns\HandlesBulkPermissions;

class ComplaintPolicy
{
    use HandlesBulkPermissions;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageComplaint);
    }

    public function view(User $user, Complaint $complaint): bool
    {
        if (! $user->hasPermissionTo(PermissionType::ManageComplaint)) {
            return false;
        }

        return $this->isWithinUserScope($user, $complaint);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageComplaint)
            && ! $user->hasRole('pimpinan');
    }

    public function update(User $user, Complaint $complaint): bool
    {
        if ($user->hasRole('pimpinan')) {
            return false;
        }

        if (! $user->hasPermissionTo(PermissionType::ManageComplaint)) {
            return false;
        }

        return $this->isWithinUserScope($user, $complaint);
    }

    public function delete(User $user, Complaint $complaint): bool
    {
        return $this->update($user, $complaint);
    }

    public function restore(User $user, Complaint $complaint): bool
    {
        return $this->update($user, $complaint);
    }

    public function forceDelete(User $user, Complaint $complaint): bool
    {
        return $user->hasRole('administrator');
    }

    protected function isWithinUserScope(User $user, Complaint $complaint): bool
    {
        if ($user->hasRole('administrator') || $user->hasRole('pimpinan')) {
            return true;
        }

        if ($user->village_id) {
            return $complaint->village_id === $user->village_id;
        }

        if ($user->district_id) {
            if ($complaint->district_id) {
                return $complaint->district_id === $user->district_id;
            }

            if ($complaint->village_id) {
                return Village::where('id', $complaint->village_id)
                    ->where('district_id', $user->district_id)
                    ->exists();
            }

            return false;
        }

        return $complaint->assigned_to === $user->id || empty($user->district_id);
    }
}
