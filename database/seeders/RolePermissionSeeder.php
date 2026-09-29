<?php

namespace Database\Seeders;

use App\Enums\PermissionType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cache permission Spatie
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Buat semua permission dari App\Enums\PermissionType
        $permissions = [];
        foreach (PermissionType::cases() as $case) {
            $permissions[$case->value] = Permission::firstOrCreate([
                'name' => $case->value,
                'guard_name' => 'web',
            ]);
        }

        // 2. Buat Role default
        $adminRole = Role::firstOrCreate(['name' => 'administrator', 'guard_name' => 'web']);
        $operatorRole = Role::firstOrCreate(['name' => 'operator', 'guard_name' => 'web']);
        $pimpinanRole = Role::firstOrCreate(['name' => 'pimpinan', 'guard_name' => 'web']);

        // 3. Beri permissions ke masing-masing Role sesuai matriks SAPA SOSIAL
        // Administrator: seluruh permission
        $adminRole->syncPermissions(array_values($permissions));

        // Operator: layanan transaksi, kasus, serta dashboard & laporan (baca)
        $operatorRole->syncPermissions([
            PermissionType::ManageServiceRequest->value,
            PermissionType::ManageComplaint->value,
            PermissionType::ManageRehabilitationClient->value,
            PermissionType::ManageRehabilitationCase->value,
            PermissionType::ViewDashboard->value,
            PermissionType::ViewReport->value,
            PermissionType::ExportReport->value,
        ]);

        // Pimpinan: persetujuan berjenjang, dashboard, serta laporan & ekspor
        $pimpinanRole->syncPermissions([
            PermissionType::ApproveDtsenLetter->value,
            PermissionType::ApprovePbiRecommendation->value,
            PermissionType::ViewDashboard->value,
            PermissionType::ViewReport->value,
            PermissionType::ExportReport->value,
        ]);

        // 4. Assign role ke user bawaan jika ada
        $adminEmails = [
            'adi@adi.com',
            'hilmi@hilmi.com',
            'admin@dinsos.blitarkab.go.id',
        ];
        foreach ($adminEmails as $email) {
            $user = User::where('email', $email)->first();
            if ($user && ! $user->hasRole('administrator')) {
                $user->assignRole($adminRole);
            }
        }

        $pimpinanEmails = [
            'kadis@dinsos.blitarkab.go.id',
            'kabid.linjamsos@dinsos.blitarkab.go.id',
            'kabid.rehsos@dinsos.blitarkab.go.id',
        ];
        foreach ($pimpinanEmails as $email) {
            $user = User::where('email', $email)->first();
            if ($user && ! $user->hasRole('pimpinan')) {
                $user->assignRole($pimpinanRole);
            }
        }

        $operatorEmails = [
            'petugas.layanan@dinsos.blitarkab.go.id',
            'petugas.pbi@dinsos.blitarkab.go.id',
            'petugas.rehsos@dinsos.blitarkab.go.id',
            'operator.kanigoro@blitarkab.go.id',
            'operator.satreyan@blitarkab.go.id',
            'operator.wlingi@blitarkab.go.id',
        ];
        foreach ($operatorEmails as $email) {
            $user = User::where('email', $email)->first();
            if ($user && ! $user->hasRole('operator')) {
                $user->assignRole($operatorRole);
            }
        }
    }
}
