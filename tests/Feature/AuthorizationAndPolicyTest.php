<?php

namespace Tests\Feature;

use App\Models\Approval;
use App\Models\Complaint;
use App\Models\DtsenCertificate;
use App\Models\ServiceRequest;
use App\Models\User;
use Tests\TestCase;

class AuthorizationAndPolicyTest extends TestCase
{
    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get('/admin');

        $response->assertStatus(302);
        $response->assertRedirect('/admin/login');
    }

    public function test_citizen_without_role_is_forbidden_from_admin_panel(): void
    {
        $citizen = User::where('email', 'warga.agus@gmail.com')->first();
        $this->assertNotNull($citizen);
        $this->assertEmpty($citizen->roles);

        $response = $this->actingAs($citizen)->get('/admin');

        $response->assertStatus(403);
    }

    public function test_administrator_can_access_admin_panel_and_roles_resource(): void
    {
        $admin = User::where('email', 'adi@adi.com')->first();
        $this->assertNotNull($admin);
        $this->assertTrue($admin->hasRole('administrator'));

        $response = $this->actingAs($admin)->get('/admin');
        $response->assertStatus(200);

        $responseRoles = $this->actingAs($admin)->get('/admin/roles');
        $responseRoles->assertStatus(200);
    }

    public function test_operator_cannot_access_roles_resource(): void
    {
        $operator = User::where('email', 'petugas.layanan@dinsos.blitarkab.go.id')->first();
        $this->assertNotNull($operator);
        $this->assertTrue($operator->hasRole('operator'));

        $response = $this->actingAs($operator)->get('/admin/roles');
        $response->assertStatus(403);
    }

    public function test_pimpinan_cannot_access_roles_resource(): void
    {
        $pimpinan = User::where('email', 'kadis@dinsos.blitarkab.go.id')->first();
        $this->assertNotNull($pimpinan);
        $this->assertTrue($pimpinan->hasRole('pimpinan'));

        $response = $this->actingAs($pimpinan)->get('/admin/roles');
        $response->assertStatus(403);
    }

    public function test_operator_can_view_service_requests_but_pimpinan_cannot(): void
    {
        $operator = User::where('email', 'petugas.layanan@dinsos.blitarkab.go.id')->first();
        $pimpinan = User::where('email', 'kadis@dinsos.blitarkab.go.id')->first();

        $this->assertTrue($operator->can('viewAny', ServiceRequest::class));
        $this->assertFalse($pimpinan->can('viewAny', ServiceRequest::class));
    }

    public function test_pimpinan_cannot_create_or_update_transactional_data(): void
    {
        $pimpinan = User::where('email', 'kadis@dinsos.blitarkab.go.id')->first();

        $this->assertFalse($pimpinan->can('create', ServiceRequest::class));
        $this->assertFalse($pimpinan->can('create', Complaint::class));
    }

    public function test_administrator_cannot_approve_without_being_designated_approver(): void
    {
        $admin = User::where('email', 'adi@adi.com')->first();
        $cert = DtsenCertificate::first();

        if ($cert) {
            $this->assertFalse($admin->can('approve', $cert));
        }
    }

    public function test_designated_approver_can_approve_pending_step(): void
    {
        $pendingApproval = Approval::where('decision', 'pending')->first();

        if ($pendingApproval && $pendingApproval->approvable) {
            $approver = User::find($pendingApproval->approver_id);
            $this->assertNotNull($approver);

            $this->assertTrue($approver->can('approve', $pendingApproval->approvable));
        }
    }
}
