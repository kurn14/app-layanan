<?php

namespace App\Policies\Concerns;

use App\Models\User;

trait HandlesBulkPermissions
{
    /**
     * Apakah user boleh melakukan bulk delete?
     */
    public function deleteAny(User $user): bool
    {
        return method_exists($this, 'create')
            ? $this->create($user)
            : ! $user->hasRole('pimpinan');
    }

    /**
     * Apakah user boleh melakukan bulk restore?
     */
    public function restoreAny(User $user): bool
    {
        return $this->deleteAny($user);
    }

    /**
     * Apakah user boleh melakukan bulk force delete?
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->hasRole('administrator');
    }

    /**
     * Apakah user boleh melakukan reorder data?
     */
    public function reorder(User $user): bool
    {
        return $this->deleteAny($user);
    }
}
