<?php

namespace Tests\Feature;

use App\Livewire\DtsenApplication;
use App\Livewire\PbiApplication;
use App\Models\District;
use App\Models\DtsenPurpose;
use App\Models\ServiceRequest;
use App\Models\User;
use App\Models\Village;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ServiceApplicationSubmissionTest extends TestCase
{
    use DatabaseTransactions;

    public function test_dtsen_application_can_be_submitted_with_documents(): void
    {
        Storage::fake('local');

        $user = User::first() ?? User::factory()->create();
        $district = District::first();
        $village = Village::where('district_id', $district->id)->first();
        $purpose = DtsenPurpose::first();

        $ktpFile = UploadedFile::fake()->create('ktp_pemohon.pdf', 150, 'application/pdf');
        $kkFile = UploadedFile::fake()->create('kartu_keluarga.pdf', 200, 'application/pdf');

        $component = Livewire::actingAs($user)
            ->test(DtsenApplication::class)
            ->set('dtsen_purpose_id', $purpose->id)
            ->set('purpose_description', 'Keperluan pendaftaran KIP Kuliah 2026')
            ->set('step', 1)
            ->call('nextStep')
            ->set('applicant_name', 'Budi Santoso')
            ->set('applicant_nik', '3505010101900001')
            ->set('family_card_number', '3505010101900001')
            ->set('address', 'Jl. Merdeka No. 45 RT 01 RW 02')
            ->set('district_id', $district->id)
            ->set('village_id', $village->id)
            ->set('phone', '081234567890')
            ->set('subject_name', 'Budi Santoso')
            ->set('subject_nik', '3505010101900001')
            ->set('relationship_to_applicant', 'Diri Sendiri')
            ->set('step', 2)
            ->call('nextStep')
            ->set('ktp_file', $ktpFile)
            ->set('kk_file', $kkFile)
            ->set('step', 3)
            ->call('nextStep')
            ->set('agree_terms', true)
            ->set('step', 4)
            ->call('submit');

        $component->assertHasNoErrors();
        $component->assertRedirect();

        $serviceRequest = ServiceRequest::where('applicant_nik', '3505010101900001')
            ->latest()
            ->first();

        $this->assertNotNull($serviceRequest);
        $this->assertCount(2, $serviceRequest->documents);

        foreach ($serviceRequest->documents as $document) {
            $this->assertNotNull($document->service_requirement_id, 'Dokumen harus terhubung dengan service_requirement_id.');
            $this->assertNotNull($document->requirement, 'Relasi requirement harus dapat diakses.');
            $this->assertNotEmpty($document->file_path);
            $this->assertEquals('pending', $document->verification_status->value);
        }

        $this->assertNotNull($serviceRequest->dtsenCertificate);
        $this->assertEquals('Budi Santoso', $serviceRequest->dtsenCertificate->subject_name);
    }

    public function test_pbi_application_can_be_submitted_with_documents(): void
    {
        Storage::fake('local');

        $user = User::first() ?? User::factory()->create();
        $district = District::first();
        $village = Village::where('district_id', $district->id)->first();

        $ktpFile = UploadedFile::fake()->create('ktp_peserta.pdf', 150, 'application/pdf');
        $kkFile = UploadedFile::fake()->create('kartu_keluarga.pdf', 200, 'application/pdf');
        $bpjsFile = UploadedFile::fake()->create('kartu_bpjs.pdf', 120, 'application/pdf');
        $faskesFile = UploadedFile::fake()->create('surat_faskes.pdf', 180, 'application/pdf');

        $component = Livewire::actingAs($user)
            ->test(PbiApplication::class)
            ->set('applicant_name', 'Siti Rahmawati')
            ->set('applicant_nik', '3505020202950002')
            ->set('family_card_number', '3505020202950002')
            ->set('address', 'Dusun Sukamaju RT 03 RW 01')
            ->set('district_id', $district->id)
            ->set('village_id', $village->id)
            ->set('phone', '081298765432')
            ->set('step', 1)
            ->call('nextStep')
            ->set('participant_name', 'Siti Rahmawati')
            ->set('participant_nik', '3505020202950002')
            ->set('bpjs_card_number', '0001234567890')
            ->set('reason', 'chronic')
            ->set('health_facility_name', 'RSUD Ngudi Waluyo Wlingi')
            ->set('health_letter_number', '445/123/RSUD/2026')
            ->set('step', 2)
            ->call('nextStep')
            ->set('ktp_file', $ktpFile)
            ->set('kk_file', $kkFile)
            ->set('bpjs_file', $bpjsFile)
            ->set('faskes_file', $faskesFile)
            ->set('step', 3)
            ->call('nextStep')
            ->set('agree_terms', true)
            ->set('step', 4)
            ->call('submit');

        $component->assertHasNoErrors();
        $component->assertRedirect();

        $serviceRequest = ServiceRequest::where('applicant_nik', '3505020202950002')
            ->latest()
            ->first();

        $this->assertNotNull($serviceRequest);
        $this->assertCount(4, $serviceRequest->documents);

        foreach ($serviceRequest->documents as $document) {
            $this->assertNotNull($document->service_requirement_id, 'Dokumen PBI harus terhubung dengan service_requirement_id.');
            $this->assertNotNull($document->requirement, 'Relasi requirement dokumen PBI harus valid.');
            $this->assertNotEmpty($document->file_path);
            $this->assertEquals('pending', $document->verification_status->value);
        }

        $this->assertNotNull($serviceRequest->pbiReactivation);
        $this->assertEquals('0001234567890', $serviceRequest->pbiReactivation->bpjs_card_number);
    }
}
