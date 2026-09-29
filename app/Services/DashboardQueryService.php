<?php

namespace App\Services;

use App\Enums\ComplaintStatus;
use App\Enums\RehabilitationCaseStatus;
use App\Enums\ServiceRequestStatus;
use App\Models\Complaint;
use App\Models\DtsenCertificate;
use App\Models\PbiReactivation;
use App\Models\RehabilitationCase;
use App\Models\ServiceRequest;
use App\Models\User;
use App\Models\Village;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class DashboardQueryService
{
    protected ?Carbon $startDate = null;

    protected ?Carbon $endDate = null;

    protected ?int $serviceTypeId = null;

    protected ?string $status = null;

    protected ?int $complaintCategoryId = null;

    protected ?string $handlingType = null;

    protected ?int $districtId = null;

    protected ?int $villageId = null;

    protected ?User $user = null;

    public function __construct(?array $filters = [], ?User $user = null)
    {
        $filters = $filters ?? [];
        $this->user = $user ?? auth()->user();

        if (! empty($filters['startDate'])) {
            $this->startDate = Carbon::parse($filters['startDate'])->startOfDay();
        }

        if (! empty($filters['endDate'])) {
            $this->endDate = Carbon::parse($filters['endDate'])->endOfDay();
        }

        if (! empty($filters['service_type_id'])) {
            $this->serviceTypeId = (int) $filters['service_type_id'];
        }

        if (! empty($filters['status'])) {
            $this->status = (string) $filters['status'];
        }

        if (! empty($filters['complaint_category_id'])) {
            $this->complaintCategoryId = (int) $filters['complaint_category_id'];
        }

        if (! empty($filters['handling_type'])) {
            $this->handlingType = (string) $filters['handling_type'];
        }

        // Terapkan pembatasan wilayah berdasarkan peran pengguna
        $this->resolveGeographicScope($filters);
    }

    public static function make(?array $filters = [], ?User $user = null): static
    {
        return new static($filters, $user);
    }

    /**
     * Tentukan pembatasan wilayah (Kecamatan / Desa) dengan mengutamakan scope role user.
     */
    protected function resolveGeographicScope(array $filters): void
    {
        if ($this->user && ! $this->user->hasRole('administrator') && ! $this->user->hasRole('pimpinan')) {
            // Operator Desa
            if ($this->user->village_id) {
                $this->villageId = (int) $this->user->village_id;
                $this->districtId = (int) ($this->user->village?->district_id ?? $this->user->district_id);

                return;
            }

            // Operator Kecamatan
            if ($this->user->district_id) {
                $this->districtId = (int) $this->user->district_id;
                if (! empty($filters['village_id'])) {
                    $selectedVillageId = (int) $filters['village_id'];
                    $belongsToDistrict = Village::where('id', $selectedVillageId)
                        ->where('district_id', $this->districtId)
                        ->exists();

                    if ($belongsToDistrict) {
                        $this->villageId = $selectedVillageId;
                    }
                }

                return;
            }
        }

        // Administrator, Pimpinan, atau pengguna tanpa lock wilayah
        if (! empty($filters['district_id'])) {
            $this->districtId = (int) $filters['district_id'];
        }

        if (! empty($filters['village_id'])) {
            $this->villageId = (int) $filters['village_id'];
        }
    }

    /**
     * Apakah user diperkenankan melihat modul / data rehabilitasi?
     * Operator Kecamatan/Desa disembunyikan dari modul rehabilitasi (data klien bersifat sensitif).
     */
    public function canAccessRehabilitation(): bool
    {
        if (! $this->user) {
            return false;
        }

        if ($this->user->hasRole('administrator') || $this->user->hasRole('pimpinan')) {
            return true;
        }

        // Jika user adalah operator wilayah (memiliki district_id atau village_id), batasi
        if ($this->user->hasRole('operator') && ($this->user->district_id || $this->user->village_id)) {
            return false;
        }

        return true;
    }

    public function getDistrictId(): ?int
    {
        return $this->districtId;
    }

    public function getVillageId(): ?int
    {
        return $this->villageId;
    }

    public function getStartDate(): ?Carbon
    {
        return $this->startDate;
    }

    public function getEndDate(): ?Carbon
    {
        return $this->endDate;
    }

    /**
     * Query dasar Pengajuan Layanan (ServiceRequest).
     */
    public function serviceRequestsQuery(): Builder
    {
        $query = ServiceRequest::query();

        if ($this->startDate) {
            $query->where('submitted_at', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->where('submitted_at', '<=', $this->endDate);
        }

        if ($this->serviceTypeId) {
            $query->where('service_type_id', $this->serviceTypeId);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->villageId) {
            $query->where('village_id', $this->villageId);
        } elseif ($this->districtId) {
            $query->whereHas('village', fn (Builder $q) => $q->where('district_id', $this->districtId));
        }

        return $query;
    }

    /**
     * Query SK DTSEN (DtsenCertificate).
     * Filter periode menggunakan `issued_at` (saat surat terbit).
     */
    public function dtsenCertificatesQuery(): Builder
    {
        $query = DtsenCertificate::query();

        if ($this->startDate) {
            $query->where('issued_at', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->where('issued_at', '<=', $this->endDate);
        }

        // Terapkan scope wilayah & status lewat relasi serviceRequest
        $query->whereHas('serviceRequest', function (Builder $q) {
            if ($this->villageId) {
                $q->where('village_id', $this->villageId);
            } elseif ($this->districtId) {
                $q->whereHas('village', fn (Builder $vq) => $vq->where('district_id', $this->districtId));
            }

            if ($this->status) {
                $q->where('status', $this->status);
            }
        });

        return $query;
    }

    /**
     * Query Reaktivasi PBI-JK.
     */
    public function pbiReactivationsQuery(): Builder
    {
        $query = PbiReactivation::query();

        // Scope lewat relasi serviceRequest
        $query->whereHas('serviceRequest', function (Builder $q) {
            if ($this->startDate) {
                $q->where('submitted_at', '>=', $this->startDate);
            }

            if ($this->endDate) {
                $q->where('submitted_at', '<=', $this->endDate);
            }

            if ($this->villageId) {
                $q->where('village_id', $this->villageId);
            } elseif ($this->districtId) {
                $q->whereHas('village', fn (Builder $vq) => $vq->where('district_id', $this->districtId));
            }

            if ($this->status) {
                $q->where('status', $this->status);
            }
        });

        return $query;
    }

    /**
     * Query Pengaduan Sosial (Complaint).
     * Filter periode menggunakan `reported_at`.
     */
    public function complaintsQuery(): Builder
    {
        $query = Complaint::query();

        if ($this->startDate) {
            $query->where('reported_at', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->where('reported_at', '<=', $this->endDate);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->complaintCategoryId) {
            $query->where('complaint_category_id', $this->complaintCategoryId);
        }

        if ($this->villageId) {
            $query->where('village_id', $this->villageId);
        } elseif ($this->districtId) {
            $query->whereHas('village', fn (Builder $q) => $q->where('district_id', $this->districtId));
        }

        return $query;
    }

    /**
     * Query Kasus Rehabilitasi (RehabilitationCase).
     * Filter periode menggunakan `received_at`.
     */
    public function rehabilitationCasesQuery(): Builder
    {
        if (! $this->canAccessRehabilitation()) {
            return RehabilitationCase::query()->whereRaw('1 = 0');
        }

        $query = RehabilitationCase::query();

        if ($this->startDate) {
            $query->where('received_at', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->where('received_at', '<=', $this->endDate);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->handlingType) {
            $query->where('handling_type', $this->handlingType);
        }

        if ($this->villageId || $this->districtId) {
            $query->whereHas('client', function (Builder $cq) {
                if ($this->villageId) {
                    $cq->where('village_id', $this->villageId);
                } elseif ($this->districtId) {
                    $cq->whereHas('village', fn (Builder $vq) => $vq->where('district_id', $this->districtId));
                }
            });
        }

        return $query;
    }

    /**
     * Daftar status akhir ("selesai" / closed) per modul.
     */
    public static function completedServiceRequestStatuses(): array
    {
        return [
            ServiceRequestStatus::COMPLETED->value,
            ServiceRequestStatus::ISSUED->value,
            ServiceRequestStatus::REACTIVATED->value,
            ServiceRequestStatus::REJECTED->value,
            ServiceRequestStatus::MINISTRY_REJECTED->value,
        ];
    }

    public static function completedComplaintStatuses(): array
    {
        return [
            ComplaintStatus::RESOLVED->value,
            ComplaintStatus::DUPLICATE->value,
            ComplaintStatus::INVALID->value,
        ];
    }

    public static function completedRehabilitationStatuses(): array
    {
        return [
            RehabilitationCaseStatus::CLOSED->value,
        ];
    }
}
