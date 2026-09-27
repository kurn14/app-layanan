<?php

namespace Tests\Feature;

use App\Enums\ComplaintAttachmentType;
use App\Livewire\ComplaintSubmission;
use App\Models\Complaint;
use App\Models\ComplaintAttachment;
use App\Models\ComplaintCategory;
use App\Models\District;
use App\Models\User;
use App\Models\Village;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ComplaintSubmissionTest extends TestCase
{
    use DatabaseTransactions;

    public function test_complaint_can_be_submitted_with_photo_attachment_to_public_storage(): void
    {
        Storage::fake('public');

        $category = ComplaintCategory::first() ?? ComplaintCategory::factory()->create();
        $district = District::first();
        $village = Village::where('district_id', $district->id)->first();

        $photoFile = UploadedFile::fake()->image('bukti_kondisi.jpg', 600, 400);

        $component = Livewire::test(ComplaintSubmission::class)
            ->set('complaint_category_id', $category->id)
            ->set('reporter_name', 'Siti Rahmawati')
            ->set('reporter_phone', '081234567899')
            ->set('district_id', $district->id)
            ->set('village_id', $village->id)
            ->set('location_detail', 'Dekat pos kamling RT 01 RW 02')
            ->set('description', 'Ditemukan seorang lansia terlantar membutuhkan bantuan medis dan permakanan mendesak.')
            ->set('attachment_file', $photoFile)
            ->set('agree_terms', true)
            ->call('submit');

        $component->assertHasNoErrors();

        $complaint = Complaint::where('reporter_phone', '081234567899')->latest('id')->first();
        $this->assertNotNull($complaint);

        $attachment = ComplaintAttachment::where('complaint_id', $complaint->id)->first();
        $this->assertNotNull($attachment);
        $this->assertEquals(ComplaintAttachmentType::PHOTO, $attachment->type);

        // Verify it was uploaded to storage/app/public
        Storage::disk('public')->assertExists($attachment->file_path);
        $this->assertStringStartsWith('complaint-attachments/'.$complaint->id, $attachment->file_path);
    }

    public function test_admin_can_view_complaint_with_attachment_in_dashboard(): void
    {
        $admin = User::first() ?? User::factory()->create();
        $complaint = Complaint::with('attachments')->first();

        $this->assertNotNull($complaint);

        $response = $this->actingAs($admin)->get('/admin/complaints/'.$complaint->id);
        $response->assertStatus(200);
        $response->assertSee('Lampiran Foto & Bukti Pendukung');
    }
}
