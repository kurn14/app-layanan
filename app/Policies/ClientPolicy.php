<?php

namespace App\Policies;

use App\Enums\PermissionType;
use App\Models\Client;
use App\Models\User;
use App\Policies\Concerns\HandlesBulkPermissions;

class ClientPolicy
{
    use HandlesBulkPermissions;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageRehabilitationClient);
    }

    public function view(User $user, Client $client): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageRehabilitationClient);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageRehabilitationClient)
            && ! $user->hasRole('pimpinan');
    }

    public function update(User $user, Client $client): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageRehabilitationClient)
            && ! $user->hasRole('pimpinan');
    }

    public function delete(User $user, Client $client): bool
    {
        return $this->update($user, $client);
    }

    public function restore(User $user, Client $client): bool
    {
        return $this->update($user, $client);
    }

    public function forceDelete(User $user, Client $client): bool
    {
        return $user->hasRole('administrator');
    }
}
