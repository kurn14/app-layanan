<div class="w-full max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
    <!-- Breadcrumb -->
    <nav aria-label="Breadcrumb" class="flex items-center space-x-2 text-xs text-on-surface-variant mb-6">
        <a href="{{ route('home') }}" wire:navigate class="hover:text-primary transition-colors flex items-center gap-1">
            <span class="material-symbols-outlined text-sm">home</span>
            Beranda
        </a>
        <span class="material-symbols-outlined text-xs text-slate-400">chevron_right</span>
        <span class="text-primary font-bold">Informasi Layanan &amp; FAQ</span>
    </nav>

    <!-- Header & Search -->
    <div class="bg-white rounded-2xl border border-slate-200 custom-shadow-card p-6 md:p-8 mb-8 text-center">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-100 text-blue-800 text-xs font-bold mb-3">
            <span class="material-symbols-outlined text-sm">help</span>
            Pusat Bantuan Warga
        </div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-inverse-surface tracking-tight">
            Informasi Pelayanan, FAQ &amp; Formulir Unduhan
        </h1>
        <p class="text-xs md:text-sm text-on-surface-variant mt-2 max-w-xl mx-auto leading-relaxed">
            Temukan panduan lengkap prosedur pelayanan, jawaban pertanyaan populer, dan unduh berkas formulir resmi.
        </p>

        <!-- Search Input -->
        <div class="max-w-xl mx-auto mt-6">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                    <span class="material-symbols-outlined text-xl">search</span>
                </span>
                <input
                    wire:model.live.debounce.300ms="search"
                    type="text"
                    placeholder="Ketik kata kunci pertanyaan atau informasi yang dicari..."
                    class="w-full min-h-[48px] pl-11 pr-4 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-primary shadow-xs"
                />
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex items-center justify-center gap-2 mb-8">
        <button
            wire:click="$set('activeSection', 'faq')"
            class="px-5 py-2.5 rounded-full text-xs font-bold transition-all {{ $activeSection === 'faq' ? 'bg-primary text-white shadow-xs' : 'bg-white border border-slate-300 text-slate-700 hover:bg-slate-50' }}"
        >
            Tanya Jawab (FAQ)
        </button>
        <button
            wire:click="$set('activeSection', 'forms')"
            class="px-5 py-2.5 rounded-full text-xs font-bold transition-all {{ $activeSection === 'forms' ? 'bg-primary text-white shadow-xs' : 'bg-white border border-slate-300 text-slate-700 hover:bg-slate-50' }}"
        >
            Formulir Unduhan
        </button>
        <button
            wire:click="$set('activeSection', 'articles')"
            class="px-5 py-2.5 rounded-full text-xs font-bold transition-all {{ $activeSection === 'articles' ? 'bg-primary text-white shadow-xs' : 'bg-white border border-slate-300 text-slate-700 hover:bg-slate-50' }}"
        >
            Panduan &amp; Program
        </button>
    </div>

    <!-- SECTION: FAQ LIST -->
    @if ($activeSection === 'faq')
        <div class="space-y-4" x-data="{ activeIndex: 0 }">
            @forelse ($faqs as $index => $faq)
                <div class="bg-white rounded-xl border border-slate-200 overflow-hidden custom-shadow-card">
                    <button
                        type="button"
                        @click="activeIndex = activeIndex === {{ $index }} ? null : {{ $index }}"
                        class="w-full flex items-center justify-between p-5 text-left hover:bg-slate-50 transition-colors"
                    >
                        <span class="text-sm md:text-base font-bold text-inverse-surface">{{ $faq->question }}</span>
                        <span class="material-symbols-outlined text-xl text-primary transition-transform duration-200" :class="{ 'rotate-180': activeIndex === {{ $index }} }">
                            keyboard_arrow_down
                        </span>
                    </button>
                    <div x-show="activeIndex === {{ $index }}" x-collapse style="display: none;" class="px-5 pb-5 pt-1 text-xs md:text-sm text-slate-600 bg-slate-50/50 leading-relaxed border-t border-slate-100">
                        {{ $faq->answer }}
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl p-8 text-center border border-slate-200 text-slate-500 text-xs">
                    Tidak ada FAQ yang cocok dengan kata kunci pencarian Anda.
                </div>
            @endforelse
        </div>
    @endif

    <!-- SECTION: DOWNLOADABLE FORMS -->
    @if ($activeSection === 'forms')
        <div class="bg-white rounded-2xl border border-slate-200 custom-shadow-card p-6 md:p-8">
            <h2 class="text-lg font-bold text-inverse-surface mb-2">Formulir Unduhan Resmi</h2>
            <p class="text-xs text-on-surface-variant mb-6">Unduh format surat pernyataan atau formulir pengajuan fisik:</p>

            <div class="space-y-3">
                @forelse ($forms as $form)
                    @php
                        $ext = strtoupper(pathinfo($form->file_path ?? 'pdf', PATHINFO_EXTENSION) ?: 'PDF');
                        $fileSize = 'PDF';
                        if ($form->file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($form->file_path)) {
                            $bytes = \Illuminate\Support\Facades\Storage::disk('public')->size($form->file_path);
                            $fileSize = round($bytes / 1024, 1) . ' KB';
                        }
                    @endphp
                    <div class="flex items-center justify-between p-4 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-slate-50 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-teal-100 text-primary flex items-center justify-center">
                                <span class="material-symbols-outlined text-xl">description</span>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900">{{ $form->name }}</h4>
                                <span class="text-[11px] text-slate-400">Versi {{ $form->version ?? '1.0' }} &bull; {{ $ext }} ({{ $fileSize }})</span>
                            </div>
                        </div>
                        <a href="{{ route('formulir.download', $form->id) }}" class="px-4 py-2 rounded-lg bg-primary text-white font-semibold text-xs hover:bg-primary-container flex items-center gap-1.5 shadow-xs transition-colors">
                            <span class="material-symbols-outlined text-sm">download</span>
                            <span>Unduh</span>
                        </a>
                    </div>
                @empty
                    <div class="p-8 text-center border border-slate-200 rounded-xl text-slate-500 text-xs">
                        Belum ada formulir resmi yang tersedia untuk diunduh saat ini.
                    </div>
                @endforelse
            </div>
        </div>
    @endif

    <!-- SECTION: ARTICLES / PANDUAN -->
    @if ($activeSection === 'articles')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse ($articles as $art)
                <div class="bg-white rounded-xl border border-slate-200 p-6 custom-shadow-card flex flex-col justify-between">
                    <div>
                        <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[11px] font-semibold">
                            {{ $art->category }}
                        </span>
                        <h3 class="text-base font-bold text-inverse-surface mt-2 mb-2">{{ $art->title }}</h3>
                        <p class="text-xs text-on-surface-variant line-clamp-3 leading-relaxed mb-4">
                            {{ $art->description }}
                        </p>
                    </div>
                    <a href="{{ route('layanan.detail', $art->slug) }}" wire:navigate class="text-xs font-semibold text-primary hover:underline flex items-center gap-1">
                        <span>Baca Selengkapnya</span>
                        <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </a>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-xl p-8 text-center border border-slate-200 text-slate-500 text-xs">
                    Belum ada artikel panduan yang dipublikasikan.
                </div>
            @endforelse
        </div>
    @endif
</div>
