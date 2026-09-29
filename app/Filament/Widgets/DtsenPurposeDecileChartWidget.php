<?php

namespace App\Filament\Widgets;

use App\Models\DtsenPurpose;
use App\Services\DashboardQueryService;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\ChartWidget\Concerns\HasFiltersSchema;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class DtsenPurposeDecileChartWidget extends ChartWidget
{
    use HasFiltersSchema, InteractsWithPageFilters;

    protected static ?int $sort = 4;

    protected ?string $heading = 'Distribusi SK DTSEN Terbit';

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'lg' => 1,
    ];

    public static function canView(): bool
    {
        $user = auth()->user();

        return $user && ! $user->hasRole('masyarakat');
    }

    public function filtersSchema(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('breakdown_by')
                ->label('Tampilkan Berdasarkan')
                ->options([
                    'purpose' => 'Tujuan Penggunaan Surat',
                    'decile' => 'Peringkat Desil SIKS-NG (1–10)',
                ])
                ->default('purpose')
                ->selectablePlaceholder(false),
        ]);
    }

    protected function getData(): array
    {
        $breakdownBy = $this->filters['breakdown_by'] ?? 'purpose';
        $queryService = DashboardQueryService::make($this->pageFilters);

        $baseQuery = (clone $queryService->dtsenCertificatesQuery())
            ->whereNotNull('issued_at');

        if ($breakdownBy === 'decile') {
            return $this->getDecileBreakdown($baseQuery);
        }

        return $this->getPurposeBreakdown($baseQuery);
    }

    /**
     * Breakdown SK DTSEN per Tujuan Penggunaan.
     */
    protected function getPurposeBreakdown($query): array
    {
        $purposes = DtsenPurpose::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();

        $countsByPurpose = (clone $query)
            ->selectRaw('dtsen_purpose_id, count(*) as count')
            ->groupBy('dtsen_purpose_id')
            ->pluck('count', 'dtsen_purpose_id')
            ->all();

        $labels = [];
        $data = [];
        foreach ($purposes as $purposeId => $name) {
            $labels[] = $name;
            $data[] = (int) ($countsByPurpose[$purposeId] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Surat Terbit',
                    'data' => $data,
                    'backgroundColor' => [
                        '#0ea5e9', // Sky
                        '#14b8a6', // Teal
                        '#6366f1', // Indigo
                        '#f59e0b', // Amber
                        '#8b5cf6', // Purple
                        '#ec4899', // Pink
                        '#10b981', // Emerald
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    /**
     * Breakdown SK DTSEN per Peringkat Desil (1-10 + Non-Desil).
     */
    protected function getDecileBreakdown($query): array
    {
        $countsByDecile = (clone $query)
            ->selectRaw('decile, count(*) as count')
            ->groupBy('decile')
            ->pluck('count', 'decile')
            ->all();

        $labels = [];
        $data = [];

        for ($i = 1; $i <= 10; $i++) {
            $labels[] = "Desil {$i}";
            $data[] = (int) ($countsByDecile[$i] ?? 0);
        }

        $nonDecileCount = (int) ($countsByDecile[null] ?? ($countsByDecile[0] ?? 0));
        $labels[] = 'Non-Desil / Lainnya';
        $data[] = $nonDecileCount;

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Penerima Surat',
                    'data' => $data,
                    'backgroundColor' => [
                        '#059669', // Desil 1 (Sangat Miskin)
                        '#10b981', // Desil 2 (Miskin)
                        '#34d399', // Desil 3 (Hampir Miskin)
                        '#f59e0b', // Desil 4 (Rentan)
                        '#fbbf24', // Desil 5 (Menengah Bawah)
                        '#94a3b8', // Desil 6
                        '#64748b', // Desil 7
                        '#475569', // Desil 8
                        '#334155', // Desil 9
                        '#1e293b', // Desil 10
                        '#e2e8f0', // Non-Desil
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
