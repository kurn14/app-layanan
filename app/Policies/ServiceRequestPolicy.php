<?php

namespace App\Policies;

use App\Enums\PermissionType;
use App\Models\ServiceRequest;
use App\Models\User;
use App\Models\Village;
use App\Policies\Concerns\HandlesBulkPermissions;

class ServiceRequestPolicy
{
    use HandlesBulkPermissions;

    /**
     * Apakah user boleh melihat daftar pengajuan?
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageServiceRequest);
    }

    /**
     * Apakah user boleh melihat detail pengajuan ini?
     * Operator wilayah hanya bisa lihat data di wilayahnya.
     */
    public function view(User $user, ServiceRequest $serviceRequest): bool
    {
        if (! $user->hasPermissionTo(PermissionType::ManageServiceRequest)) {
            return false;
        }

        return $this->isWithinUserScope($user, $serviceRequest);
    }

    /**
     * Apakah user boleh membuat pengajuan baru (atas nama warga)?
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo(PermissionType::ManageServiceRequest)
            && ! $user->hasRole('pimpinan');
    }

    /**
     * Apakah user boleh mengedit pengajuan ini?
     * Pimpinan tidak boleh mengubah data transaksi.
     */
    public function update(User $user, ServiceRequest $serviceRequest): bool
    {
        if ($user->hasRole('pimpinan')) {
            return false;
        }

        if (! $user->hasPermissionTo(PermissionType::ManageServiceRequest)) {
            return false;
        }

        return $this->isWithinUserScope($user, $serviceRequest);
    }

    /**
     * Soft delete pengajuan.
     */
    public function delete(User $user, ServiceRequest $serviceRequest): bool
    {
        return $this->update($user, $serviceRequest);
    }

    /**
     * Restore pengajuan yang di-soft delete.
     */
    public function restore(User $user, ServiceRequest $serviceRequest): bool
    {
        return $this->update($user, $serviceRequest);
    }

    /**
     * Force delete pengajuan (hanya administrator).
     */
    public function forceDelete(User $user, ServiceRequest $serviceRequest): bool
    {
        return $user->hasRole('administrator');
    }

    /**
     * Cek apakah data berada dalam lingkup wilayah atau penugasan user.
     */
    protected function isWithinUserScope(User $user, ServiceRequest $serviceRequest): bool
    {
        if ($user->hasRole('administrator') || $user->hasRole('pimpinan')) {
            return true;
        }

        // Operator wilayah desa
        if ($user->village_id) {
            return $serviceRequest->village_id === $user->village_id;
        }

        // Operator wilayah kecamatan
        if ($user->district_id) {
            if ($serviceRequest->village_id) {
                return Village::where('id', $serviceRequest->village_id)
                    ->where('district_id', $user->district_id)
                    ->exists();
            }

            return false;
        }

        // Operator staf dinas sosial (tanpa wilayah khusus)
        return $serviceRequest->officer_id === $user->id
            || $serviceRequest->work_unit_id === $user->work_unit_id
            || empty($user->district_id);
    }
}
