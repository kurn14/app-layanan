<x-filament-widgets::widget class="fi-wi-table">
    <div class="space-y-4">
        {{-- Header Native Filament Tabs Navigation --}}
        <x-filament::tabs :contained="true">
            <x-filament::tabs.item
                :active="$activeTab === 'approvals'"
                wire:click="setTab('approvals')"
                icon="heroicon-o-pencil-square"
                :badge="$this->getPendingApprovalsCount()"
                badge-color="warning"
            >
                Menunggu Paraf / TTD
            </x-filament::tabs.item>

            <x-filament::tabs.item
                :active="$activeTab === 'emergency'"
                wire:click="setTab('emergency')"
                icon="heroicon-o-exclamation-triangle"
                :badge="$this->getEmergencyCount()"
                badge-color="danger"
            >
                Darurat Medis Belum Selesai
            </x-filament::tabs.item>

            <x-filament::tabs.item
                :active="$activeTab === 'stalled_pbi'"
                wire:click="setTab('stalled_pbi')"
                icon="heroicon-o-clock"
                :badge="$this->getStalledPbiCount()"
                badge-color="warning"
            >
                PBI-JK Tertahan Kemensos (&gt; 3 Hari)
            </x-filament::tabs.item>
        </x-filament::tabs>

        {{-- Table Rendering --}}
        <div>
            {{ $this->table }}
        </div>
    </div>
</x-filament-widgets::widget>
