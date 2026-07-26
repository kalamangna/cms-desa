<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Bersihkan cache permission bawaan Spatie
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $role = Role::where('name', 'admin_desa')->first();
        if ($role) {
            // Ambil SEMUA izin yang ada di database saat ini
            $allPermissions = Permission::all();

            $sensitivePermissions = [
                'View:Role', 'ViewAny:Role', 'Create:Role', 'Update:Role', 'Delete:Role', 'DeleteAny:Role', 'ForceDelete:Role', 'ForceDeleteAny:Role', 'Restore:Role', 'RestoreAny:Role', 'Replicate:Role', 'Reorder:Role',
                'View:User', 'ViewAny:User', 'Create:User', 'Update:User', 'Delete:User', 'DeleteAny:User', 'ForceDelete:User', 'ForceDeleteAny:User', 'Restore:User', 'RestoreAny:User', 'Replicate:User', 'Reorder:User',
                'View:AuditLog', 'ViewAny:AuditLog', 'Create:AuditLog', 'Update:AuditLog', 'Delete:AuditLog', 'DeleteAny:AuditLog', 'ForceDelete:AuditLog', 'ForceDeleteAny:AuditLog', 'Restore:AuditLog', 'RestoreAny:AuditLog', 'Replicate:AuditLog', 'Reorder:AuditLog',
                'View:Backups'
            ];

            // Filter izin yang BOLEH dimiliki oleh admin_desa
            // Yaitu semua izin KECUALI izin sensitif
            $permissionsToAssign = $allPermissions->reject(function ($permission) use ($sensitivePermissions) {
                return in_array($permission->name, $sensitivePermissions);
            });

            // Timpa ulang semua izin admin_desa dengan izin yang benar
            // Ini akan memastikan admin_desa mendapatkan ratusan izin konten, tapi tidak izin sensitif
            $role->syncPermissions($permissionsToAssign);
        }
        
        // Bersihkan cache lagi setelah operasi selesai
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
