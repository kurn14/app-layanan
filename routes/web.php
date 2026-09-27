<?php

use App\Livewire\CertificateVerification;
use App\Livewire\CitizenAccount;
use App\Livewire\CitizenAuth;
use App\Livewire\ComplaintSubmission;
use App\Livewire\DtsenApplication;
use App\Livewire\GeneralApplication;
use App\Livewire\Home;
use App\Livewire\InformationFaq;
use App\Livewire\PbiApplication;
use App\Livewire\ServiceDetail;
use App\Livewire\ServiceList;
use App\Livewire\TicketSuccess;
use App\Livewire\TicketTracking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - SAPA SOSIAL Portal Publik
|--------------------------------------------------------------------------
| Livewire v4 Full-Page Class-based Components
*/

// 1. Beranda Portal
Route::livewire('/', Home::class)->name('home');

// 2. Layanan Prioritas Pengajuan Forms (specific paths first)
Route::livewire('/layanan/dtsen/ajukan', DtsenApplication::class)->name('pengajuan.dtsen');
Route::livewire('/layanan/pbi/ajukan', PbiApplication::class)->name('pengajuan.pbi');
Route::livewire('/layanan/{slug}/ajukan', GeneralApplication::class)->name('pengajuan.umum');

// 3. Layanan Catalog & Details
Route::livewire('/layanan', ServiceList::class)->name('layanan.index');
Route::livewire('/layanan/{slug}', ServiceDetail::class)->name('layanan.detail');

// 4. Pengaduan Sosial
Route::livewire('/pengaduan', ComplaintSubmission::class)->name('pengaduan.create');

// 5. Sukses Tiket
Route::livewire('/sukses-tiket/{ticketNumber}', TicketSuccess::class)->name('sukses-tiket');

// 6. Cek Status Tiket / Pelacakan
Route::livewire('/cek-status', TicketTracking::class)->name('cek-status');

// 7. Verifikasi Keaslian Surat DTSEN via Kode / QR
Route::livewire('/verifikasi/{code?}', CertificateVerification::class)->name('verifikasi');

// 8. Informasi Layanan & FAQ
Route::livewire('/informasi-faq', InformationFaq::class)->name('informasi-faq');

// 9. Auth Warga (Masuk & Daftar)
Route::livewire('/masuk', CitizenAuth::class)->name('masuk');
Route::livewire('/login', CitizenAuth::class)->name('login');
Route::livewire('/daftar', CitizenAuth::class)->name('daftar');

Route::post('/keluar', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('home');
})->name('logout');

// 10. Area Warga / Akun Saya (Protected)
Route::middleware('auth')->group(function () {
    Route::livewire('/akun', CitizenAccount::class)->name('akun');
});
