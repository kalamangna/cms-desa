<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Menghapus peran agen_statistik jika ada
        DB::table('roles')->where('name', 'agen_statistik')->delete();

        // 2. Memperbaiki URL Visitor Logs lama (hanya mengambil path)
        DB::table('visitor_logs')->orderBy('id')->chunk(100, function ($logs) {
            foreach ($logs as $log) {
                $path = parse_url($log->url, PHP_URL_PATH) ?: '/';
                if ($log->url !== $path) {
                    DB::table('visitor_logs')->where('id', $log->id)->update(['url' => $path]);
                }
            }
        });

        // 3. Mengamankan & Menyesuaikan izin (permissions) admin_desa yang sedang aktif
        $role = Role::where('name', 'admin_desa')->first();
        if ($role) {
            // Cabut izin sensitif agar tidak bisa mengelola sistem inti
            $sensitivePermissions = [
                'View:Role', 'ViewAny:Role', 'Create:Role', 'Update:Role', 'Delete:Role', 'DeleteAny:Role', 'ForceDelete:Role', 'ForceDeleteAny:Role', 'Restore:Role', 'RestoreAny:Role', 'Replicate:Role', 'Reorder:Role',
                'View:User', 'ViewAny:User', 'Create:User', 'Update:User', 'Delete:User', 'DeleteAny:User', 'ForceDelete:User', 'ForceDeleteAny:User', 'Restore:User', 'RestoreAny:User', 'Replicate:User', 'Reorder:User',
                'View:AuditLog', 'ViewAny:AuditLog', 'Create:AuditLog', 'Update:AuditLog', 'Delete:AuditLog', 'DeleteAny:AuditLog', 'ForceDelete:AuditLog', 'ForceDeleteAny:AuditLog', 'Restore:AuditLog', 'RestoreAny:AuditLog', 'Replicate:AuditLog', 'Reorder:AuditLog',
                'View:Backups',
            ];

            $permissionsToRevoke = Permission::whereIn('name', $sensitivePermissions)->get();
            $role->revokePermissionTo($permissionsToRevoke);

            // Berikan izin spesifik ke halaman yang diizinkan (Pengaturan & Statistik)
            $allowedPages = Permission::whereIn('name', ['View:ManageSettings', 'View:VisitorStatistics'])->get();
            $role->givePermissionTo($allowedPages);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak perlu direverse karena ini data cleansing
    }
};
