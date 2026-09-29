<?php

namespace App\Policies;

use App\Enums\PermissionType;
use App\Models\ComplaintAttachment;
use App\Models\User;
use App\Policies\Concerns\HandlesBulkPermissions;

class ComplaintAttachmentPolicy
{
    use HandlesBulkPermissions;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageComplaint);
    }

    public function view(User $user, ComplaintAttachment $attachment): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageComplaint);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageComplaint)
            && ! $user->hasRole('pimpinan');
    }

    public function update(User $user, ComplaintAttachment $attachment): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageComplaint)
            && ! $user->hasRole('pimpinan');
    }

    public function delete(User $user, ComplaintAttachment $attachment): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageComplaint)
            && ! $user->hasRole('pimpinan');
    }

    public function restore(User $user, ComplaintAttachment $attachment): bool
    {
        return $this->update($user, $attachment);
    }

    public function forceDelete(User $user, ComplaintAttachment $attachment): bool
    {
        return $user->hasRole('administrator');
    }
}
