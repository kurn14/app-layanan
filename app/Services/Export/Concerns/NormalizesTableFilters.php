<?php

namespace App\Services\Export\Concerns;

trait NormalizesTableFilters
{
    /**
     * Filament menyimpan state filter tabel dalam bentuk array bersarang
     * (mis. SelectFilter => ['value' => ...], Filter toggle => ['isActive' => ...]).
     * Ratakan menjadi array datar agar dapat dikonsumsi oleh service query
     * (mis. DashboardQueryService) baik saat diunduh langsung maupun via queue job.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    protected function normalizeFilters(array $filters): array
    {
        $flat = [];

        foreach ($filters as $key => $value) {
            if (is_array($value) && array_key_exists('value', $value)) {
                $flat[$key] = $value['value'];
            } elseif (is_array($value) && array_key_exists('isActive', $value)) {
                $flat[$key] = $value['isActive'];
            } else {
                $flat[$key] = $value;
            }
        }

        return $flat;
    }
}
