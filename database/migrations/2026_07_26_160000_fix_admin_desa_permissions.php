<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Bersihkan cache permission bawaan Spatie
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Generate ulang seluruh permission secara otomatis dan pasangkan ke admin_desa
        // Karena panel_user di config/filament-shield.php sudah kita ganti menjadi admin_desa,
        // Shield akan otomatis menempelkan SELURUH permission baru ke admin_desa.
        Artisan::call('shield:generate', [
            '--all' => true,
            '--panel' => 'admin',
            '--option' => 'policies_and_permissions',
        ]);

        // 2. Cabut kembali izin sensitif dari admin_desa (seperti konfigurasi sebelumnya)
        $role = Role::where('name', 'admin_desa')->first();
        if ($role) {
            $sensitivePermissions = [
                'View:Role', 'ViewAny:Role', 'Create:Role', 'Update:Role', 'Delete:Role', 'DeleteAny:Role', 'ForceDelete:Role', 'ForceDeleteAny:Role', 'Restore:Role', 'RestoreAny:Role', 'Replicate:Role', 'Reorder:Role',
                'View:User', 'ViewAny:User', 'Create:User', 'Update:User', 'Delete:User', 'DeleteAny:User', 'ForceDelete:User', 'ForceDeleteAny:User', 'Restore:User', 'RestoreAny:User', 'Replicate:User', 'Reorder:User',
                'View:AuditLog', 'ViewAny:AuditLog', 'Create:AuditLog', 'Update:AuditLog', 'Delete:AuditLog', 'DeleteAny:AuditLog', 'ForceDelete:AuditLog', 'ForceDeleteAny:AuditLog', 'Restore:AuditLog', 'RestoreAny:AuditLog', 'Replicate:AuditLog', 'Reorder:AuditLog',
                'View:Backups',
            ];

            $permissionsToRevoke = Permission::whereIn('name', $sensitivePermissions)->get();
            if ($permissionsToRevoke->isNotEmpty()) {
                $role->revokePermissionTo($permissionsToRevoke);
            }

            // Berikan izin spesifik ke halaman yang diizinkan (Pengaturan & Statistik)
            $allowedPages = Permission::whereIn('name', ['View:ManageSettings', 'View:VisitorStatistics'])->get();
            if ($allowedPages->isNotEmpty()) {
                $role->givePermissionTo($allowedPages);
            }
        }

        // 3. (Opsional) Hapus peran panel_user yang terlanjur terbuat jika ada
        Role::where('name', 'panel_user')->delete();

        // Bersihkan cache lagi setelah operasi selesai
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
