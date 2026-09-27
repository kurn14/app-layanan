<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class FrontendPagesTest extends TestCase
{
    public function test_home_page_can_be_rendered(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('SAPA SOSIAL');
        $response->assertSee('Satu Pintu Layanan Sosial Kabupaten Blitar');
    }

    public function test_services_catalog_page_can_be_rendered(): void
    {
        $response = $this->get('/layanan');
        $response->assertStatus(200);
        $response->assertSee('Pusat Layanan Sosial Kabupaten Blitar');
        $response->assertSee('Surat Keterangan DTSEN');
    }

    public function test_service_detail_page_can_be_rendered(): void
    {
        $response = $this->get('/layanan/dtsen');
        $response->assertStatus(200);
        $response->assertSee('Surat Keterangan DTSEN');
        $response->assertSee('Persyaratan');
    }

    public function test_dtsen_application_form_can_be_rendered(): void
    {
        $response = $this->get('/layanan/dtsen/ajukan');
        $response->assertStatus(200);
        $response->assertSee('Pengajuan Surat Keterangan DTSEN');
    }

    public function test_pbi_application_form_can_be_rendered(): void
    {
        $response = $this->get('/layanan/pbi/ajukan');
        $response->assertStatus(200);
        $response->assertSee('Reaktivasi KIS / PBI-JK');
    }

    public function test_complaint_form_can_be_rendered(): void
    {
        $response = $this->get('/pengaduan');
        $response->assertStatus(200);
        $response->assertSee('Sampaikan Laporan & Masalah Sosial');
    }

    public function test_status_tracking_page_can_be_rendered(): void
    {
        $response = $this->get('/cek-status');
        $response->assertStatus(200);
        $response->assertSee('Lacak Status Pengajuan & Tiket');
    }

    public function test_verification_page_can_be_rendered(): void
    {
        $response = $this->get('/verifikasi');
        $response->assertStatus(200);
        $response->assertSee('Verifikasi Keaslian Surat DTSEN');
    }

    public function test_information_and_faq_page_can_be_rendered(): void
    {
        $response = $this->get('/informasi-faq');
        $response->assertStatus(200);
        $response->assertSee('Informasi Pelayanan, FAQ & Formulir Unduhan');
    }

    public function test_auth_pages_can_be_rendered(): void
    {
        $response = $this->get('/masuk');
        $response->assertStatus(200);
        $response->assertSee('Masuk ke Akun SAPA SOSIAL');

        $responseRegister = $this->get('/daftar');
        $responseRegister->assertStatus(200);
    }

    public function test_citizen_account_page_requires_authentication(): void
    {
        $response = $this->get('/akun');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_citizen_can_view_account_page(): void
    {
        $user = User::factory()->create([
            'nik' => '3505010101990001',
            'phone' => '081234567899',
        ]);

        $response = $this->actingAs($user)->get('/akun');
        $response->assertStatus(200);
        $response->assertSee('Akun Saya & Riwayat Berkas');
    }
}
