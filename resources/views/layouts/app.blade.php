<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'SAPA SOSIAL — Satu Pintu Layanan Sosial Kabupaten Blitar' }}</title>
    <meta name="description" content="Satu Pintu Layanan Sosial Kabupaten Blitar. Ajukan layanan, sampaikan pengaduan, dan pantau prosesnya secara online.">

    <!-- Google Fonts & Material Symbols -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-canvas-bg text-on-surface antialiased selection:bg-primary selection:text-on-primary min-h-screen flex flex-col font-sans" x-data="{ mobileMenuOpen: false }">

    <!-- TOP STICKY NAVBAR -->
    <header class="top-0 sticky z-50 bg-white/95 backdrop-blur-md border-b border-outline-variant/60 shadow-xs">
        <div class="flex justify-between items-center w-full px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto h-16">
            <!-- Brand & Emblem -->
            <a class="flex items-center gap-3 group focus:outline-none" href="{{ route('home') }}" wire:navigate>
                <div class="w-10 h-10 rounded-xl bg-primary-container text-white flex items-center justify-center font-bold shadow-sm group-hover:scale-105 transition-transform duration-200">
                    <span class="material-symbols-outlined text-2xl">shield</span>
                </div>
                <div class="flex flex-col">
                    <span class="text-lg font-bold text-primary tracking-tight leading-none">SAPA SOSIAL</span>
                    <span class="text-xs text-secondary mt-0.5">Dinas Sosial Kab. Blitar</span>
                </div>
            </a>

            <!-- Desktop Navigation -->
            <nav class="hidden md:flex items-center space-x-6 lg:space-x-8 text-sm font-semibold">
                <a href="{{ route('home') }}" wire:navigate class="{{ request()->routeIs('home') ? 'border-b-2 border-primary text-primary pb-1' : 'text-on-surface-variant hover:text-primary transition-colors pb-1' }}">
                    Beranda
                </a>
                <a href="{{ route('layanan.index') }}" wire:navigate class="{{ request()->routeIs('layanan.*') ? 'border-b-2 border-primary text-primary pb-1' : 'text-on-surface-variant hover:text-primary transition-colors pb-1' }}">
                    Layanan
                </a>
                <a href="{{ route('pengaduan.create') }}" wire:navigate class="{{ request()->routeIs('pengaduan.*') ? 'border-b-2 border-primary text-primary pb-1' : 'text-on-surface-variant hover:text-primary transition-colors pb-1' }}">
                    Pengaduan
                </a>
                <a href="{{ route('cek-status') }}" wire:navigate class="{{ request()->routeIs('cek-status') ? 'border-b-2 border-primary text-primary pb-1' : 'text-on-surface-variant hover:text-primary transition-colors pb-1' }}">
                    Cek Status
                </a>
                <a href="{{ route('verifikasi') }}" wire:navigate class="{{ request()->routeIs('verifikasi') ? 'border-b-2 border-primary text-primary pb-1' : 'text-on-surface-variant hover:text-primary transition-colors pb-1' }}">
                    Verifikasi Surat
                </a>
                <a href="{{ route('informasi-faq') }}" wire:navigate class="{{ request()->routeIs('informasi-faq') ? 'border-b-2 border-primary text-primary pb-1' : 'text-on-surface-variant hover:text-primary transition-colors pb-1' }}">
                    Informasi &amp; FAQ
                </a>
            </nav>

            <!-- Trailing Action: Auth / User Area -->
            <div class="hidden md:flex items-center gap-3">
                @auth
                    <a href="{{ route('akun') }}" wire:navigate class="inline-flex items-center justify-center min-h-[44px] px-4 py-2 rounded-xl bg-surface-container-low text-primary font-semibold text-sm hover:bg-surface-container transition-all border border-secondary-container">
                        <span class="material-symbols-outlined text-lg mr-1.5">person</span>
                        <span>Akun Saya</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center justify-center min-h-[44px] px-3 py-2 rounded-xl text-slate-500 hover:text-status-error hover:bg-red-50 transition-colors" title="Keluar">
                            <span class="material-symbols-outlined text-xl">logout</span>
                        </button>
                    </form>
                @else
                    <a href="{{ route('masuk') }}" wire:navigate class="inline-flex items-center justify-center min-h-[44px] px-5 py-2 rounded-xl bg-primary text-white font-semibold text-sm hover:bg-primary-container active:scale-95 transition-all shadow-xs">
                        <span class="material-symbols-outlined text-lg mr-1.5">login</span>
                        <span>Masuk</span>
                    </a>
                @endauth
            </div>

            <!-- Mobile Menu Toggle Button -->
            <div class="flex items-center md:hidden">
                <button type="button" @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 rounded-lg text-slate-600 hover:text-primary hover:bg-slate-100 focus:outline-none" aria-label="Toggle navigation">
                    <span class="material-symbols-outlined text-2xl" x-show="!mobileMenuOpen">menu</span>
                    <span class="material-symbols-outlined text-2xl" x-show="mobileMenuOpen" style="display: none;">close</span>
                </button>
            </div>
        </div>

        <!-- Mobile Navigation Drawer -->
        <div x-show="mobileMenuOpen" x-transition.origin.top style="display: none;" class="md:hidden border-t border-slate-200 bg-white px-4 pt-3 pb-6 space-y-2 shadow-lg">
            <a href="{{ route('home') }}" wire:navigate @click="mobileMenuOpen = false" class="block px-3 py-2.5 rounded-lg text-base font-medium {{ request()->routeIs('home') ? 'bg-primary/10 text-primary font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                Beranda
            </a>
            <a href="{{ route('layanan.index') }}" wire:navigate @click="mobileMenuOpen = false" class="block px-3 py-2.5 rounded-lg text-base font-medium {{ request()->routeIs('layanan.*') ? 'bg-primary/10 text-primary font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                Layanan
            </a>
            <a href="{{ route('pengaduan.create') }}" wire:navigate @click="mobileMenuOpen = false" class="block px-3 py-2.5 rounded-lg text-base font-medium {{ request()->routeIs('pengaduan.*') ? 'bg-primary/10 text-primary font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                Pengaduan
            </a>
            <a href="{{ route('cek-status') }}" wire:navigate @click="mobileMenuOpen = false" class="block px-3 py-2.5 rounded-lg text-base font-medium {{ request()->routeIs('cek-status') ? 'bg-primary/10 text-primary font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                Cek Status
            </a>
            <a href="{{ route('verifikasi') }}" wire:navigate @click="mobileMenuOpen = false" class="block px-3 py-2.5 rounded-lg text-base font-medium {{ request()->routeIs('verifikasi') ? 'bg-primary/10 text-primary font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                Verifikasi Surat
            </a>
            <a href="{{ route('informasi-faq') }}" wire:navigate @click="mobileMenuOpen = false" class="block px-3 py-2.5 rounded-lg text-base font-medium {{ request()->routeIs('informasi-faq') ? 'bg-primary/10 text-primary font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                Informasi &amp; FAQ
            </a>
            <div class="pt-4 border-t border-slate-200">
                @auth
                    <a href="{{ route('akun') }}" wire:navigate @click="mobileMenuOpen = false" class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-surface-container-low text-primary font-bold text-sm mb-2">
                        <span class="material-symbols-outlined text-lg">person</span>
                        Akun Saya ({{ Auth::user()->name }})
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-red-200 text-status-error font-semibold text-sm hover:bg-red-50">
                            <span class="material-symbols-outlined text-lg">logout</span>
                            Keluar
                        </button>
                    </form>
                @else
                    <a href="{{ route('masuk') }}" wire:navigate @click="mobileMenuOpen = false" class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-primary text-white font-bold text-sm">
                        <span class="material-symbols-outlined text-lg">login</span>
                        Masuk / Daftar Akun
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- FLASH MESSAGES -->
    @if (session()->has('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
            <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4 text-emerald-800 flex items-start gap-3">
                <span class="material-symbols-outlined text-emerald-600">check_circle</span>
                <div class="flex-1 text-sm font-medium">{{ session('success') }}</div>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
            <div class="rounded-xl bg-red-50 border border-red-200 p-4 text-red-800 flex items-start gap-3">
                <span class="material-symbols-outlined text-red-600">error</span>
                <div class="flex-1 text-sm font-medium">{{ session('error') }}</div>
            </div>
        </div>
    @endif

    <!-- MAIN BODY CONTENT -->
    <main class="flex-grow">
        {{ $slot }}
    </main>

    <!-- GLOBAL FOOTER -->
    <footer class="bg-inverse-surface text-slate-300 pt-16 pb-10 border-t border-slate-800 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-10 mb-12">
                <!-- Col 1: Identity & Description (4 cols) -->
                <div class="lg:col-span-4">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-primary-container text-white flex items-center justify-center font-bold shadow-sm">
                            <span class="material-symbols-outlined text-2xl">shield</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xl font-bold text-white tracking-tight leading-none">SAPA SOSIAL</span>
                            <span class="text-xs text-slate-400 mt-0.5">Dinas Sosial Kab. Blitar</span>
                        </div>
                    </div>
                    <p class="text-sm text-slate-400 leading-relaxed mb-6">
                        Satu Pintu Layanan Sosial Kabupaten Blitar menghadirkan pelayanan terpadu yang transparan, mudah, dan akuntabel. Setiap tahapan pengajuan tercatat dan dapat ditelusuri secara real-time.
                    </p>
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-800/80 border border-slate-700 text-xs text-slate-300">
                        <span class="w-2 h-2 rounded-full bg-status-success animate-pulse"></span>
                        <span>Sistem Aktif &bull; Layanan Terintegrasi</span>
                    </div>
                </div>

                <!-- Col 2: Layanan Prioritas (3 cols) -->
                <div class="lg:col-span-3">
                    <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-4">Layanan Utama</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li>
                            <a href="{{ route('layanan.detail', 'dtsen') }}" wire:navigate class="text-slate-400 hover:text-white transition-colors flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-base text-status-warning">arrow_right</span>
                                Surat Keterangan DTSEN
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('layanan.detail', 'pbi') }}" wire:navigate class="text-slate-400 hover:text-white transition-colors flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-base text-status-warning">arrow_right</span>
                                Reaktivasi KIS / PBI-JK
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('layanan.detail', 'rehsos') }}" wire:navigate class="text-slate-400 hover:text-white transition-colors flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-base text-status-warning">arrow_right</span>
                                Pelayanan Rehabilitasi Sosial
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('pengaduan.create') }}" wire:navigate class="text-slate-400 hover:text-white transition-colors flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-base text-status-warning">arrow_right</span>
                                Pengaduan &amp; Laporan Sosial
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('layanan.index') }}" wire:navigate class="text-slate-400 hover:text-white transition-colors flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-base text-status-warning">arrow_right</span>
                                Semua Jenis Layanan
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Col 3: Akses Cepat (2 cols) -->
                <div class="lg:col-span-2">
                    <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-4">Akses Cepat</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ route('cek-status') }}" wire:navigate class="text-slate-400 hover:text-white transition-colors">Lacak Tiket</a></li>
                        <li><a href="{{ route('verifikasi') }}" wire:navigate class="text-slate-400 hover:text-white transition-colors">Verifikasi Surat</a></li>
                        <li><a href="{{ route('informasi-faq') }}" wire:navigate class="text-slate-400 hover:text-white transition-colors">Informasi &amp; FAQ</a></li>
                        <li><a href="{{ url('/admin') }}" class="text-slate-400 hover:text-white transition-colors">Panel Petugas</a></li>
                        <li><a href="{{ route('masuk') }}" wire:navigate class="text-slate-400 hover:text-white transition-colors">Area Warga</a></li>
                    </ul>
                </div>

                <!-- Col 4: Kontak & Kantor (3 cols) -->
                <div class="lg:col-span-3">
                    <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-4">Kontak Dinas Sosial</h4>
                    <div class="space-y-3 text-sm text-slate-400">
                        <div class="flex items-start gap-2.5">
                            <span class="material-symbols-outlined text-lg text-primary-fixed mt-0.5">location_on</span>
                            <span>Jl. Mojopahit No. 5, Kota Blitar, Jawa Timur 66113</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-lg text-primary-fixed">call</span>
                            <span>(0342) 801123 / WA: 0812-3456-7890</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-lg text-primary-fixed">mail</span>
                            <span>dinsos@blitarkab.go.id</span>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="material-symbols-outlined text-lg text-primary-fixed mt-0.5">schedule</span>
                            <span>Senin &ndash; Jumat, 07.30 &ndash; 15.30 WIB</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Copyright Bar -->
            <div class="border-t border-slate-800 pt-8 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-slate-500">
                <p>&copy; {{ date('Y') }} Dinas Sosial Kabupaten Blitar. Hak cipta dilindungi undang-undang.</p>
                <div class="flex items-center gap-6">
                    <a href="{{ route('informasi-faq') }}" wire:navigate class="hover:text-slate-300 transition-colors">Ketentuan Layanan</a>
                    <a href="{{ route('informasi-faq') }}" wire:navigate class="hover:text-slate-300 transition-colors">Kebijakan Privasi</a>
                    <a href="{{ route('cek-status') }}" wire:navigate class="hover:text-slate-300 transition-colors">Bantuan Teknis</a>
                </div>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
