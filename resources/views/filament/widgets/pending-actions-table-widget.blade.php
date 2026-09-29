<x-filament-widgets::widget class="fi-wi-table">
    <div class="space-y-4">
        {{-- Header Tabs Navigation --}}
        <div class="flex flex-wrap items-center gap-2 border-b border-gray-200 pb-3 dark:border-white/10">
            <button
                type="button"
                wire:click="setTab('approvals')"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $activeTab === 'approvals' ? 'bg-primary-600 text-white shadow-sm dark:bg-primary-500' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-white/5 dark:text-gray-300 dark:hover:bg-white/10' }}"
            >
                <x-filament::icon icon="heroicon-o-pencil-square" class="h-4 w-4" />
                <span>Menunggu Paraf / TTD</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $activeTab === 'approvals' ? 'bg-white/20 text-white' : 'bg-gray-200 text-gray-800 dark:bg-white/10 dark:text-gray-200' }}">
                    {{ $this->getPendingApprovalsCount() }}
                </span>
            </button>

            <button
                type="button"
                wire:click="setTab('emergency')"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $activeTab === 'emergency' ? 'bg-rose-600 text-white shadow-sm dark:bg-rose-500' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-white/5 dark:text-gray-300 dark:hover:bg-white/10' }}"
            >
                <x-filament::icon icon="heroicon-o-exclamation-triangle" class="h-4 w-4" />
                <span>Darurat Medis Belum Selesai</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $activeTab === 'emergency' ? 'bg-white/20 text-white' : 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300' }}">
                    {{ $this->getEmergencyCount() }}
                </span>
            </button>

            <button
                type="button"
                wire:click="setTab('stalled_pbi')"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $activeTab === 'stalled_pbi' ? 'bg-amber-600 text-white shadow-sm dark:bg-amber-500' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-white/5 dark:text-gray-300 dark:hover:bg-white/10' }}"
            >
                <x-filament::icon icon="heroicon-o-clock" class="h-4 w-4" />
                <span>PBI-JK Tertahan Kemensos (&gt; 3 Hari)</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $activeTab === 'stalled_pbi' ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300' }}">
                    {{ $this->getStalledPbiCount() }}
                </span>
            </button>
        </div>

        {{-- Table Rendering --}}
        <div>
            {{ $this->table }}
        </div>
    </div>
</x-filament-widgets::widget>
