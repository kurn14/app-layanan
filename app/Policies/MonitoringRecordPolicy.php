<?php

namespace App\Policies;

use App\Enums\PermissionType;
use App\Models\MonitoringRecord;
use App\Models\User;
use App\Policies\Concerns\HandlesBulkPermissions;

class MonitoringRecordPolicy
{
    use HandlesBulkPermissions;

    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageRehabilitationCase);
    }

    public function view(User $user, MonitoringRecord $record): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageRehabilitationCase);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageRehabilitationCase)
            && ! $user->hasRole('pimpinan');
    }

    public function update(User $user, MonitoringRecord $record): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageRehabilitationCase)
            && ! $user->hasRole('pimpinan');
    }

    public function delete(User $user, MonitoringRecord $record): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageRehabilitationCase)
            && ! $user->hasRole('pimpinan');
    }

    public function restore(User $user, MonitoringRecord $record): bool
    {
        return $this->update($user, $record);
    }

    public function forceDelete(User $user, MonitoringRecord $record): bool
    {
        return $user->hasRole('administrator');
    }
}
