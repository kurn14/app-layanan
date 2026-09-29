<?php

namespace App\Policies;

use App\Enums\PermissionType;
use App\Models\ServiceRequestDocument;
use App\Models\User;
use App\Policies\Concerns\HandlesBulkPermissions;

class ServiceRequestDocumentPolicy
{
    use HandlesBulkPermissions;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageServiceRequest);
    }

    public function view(User $user, ServiceRequestDocument $document): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageServiceRequest);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageServiceRequest)
            && ! $user->hasRole('pimpinan');
    }

    public function update(User $user, ServiceRequestDocument $document): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageServiceRequest)
            && ! $user->hasRole('pimpinan');
    }

    public function delete(User $user, ServiceRequestDocument $document): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageServiceRequest)
            && ! $user->hasRole('pimpinan');
    }

    public function restore(User $user, ServiceRequestDocument $document): bool
    {
        return $this->update($user, $document);
    }

    public function forceDelete(User $user, ServiceRequestDocument $document): bool
    {
        return $user->hasRole('administrator');
    }
}
