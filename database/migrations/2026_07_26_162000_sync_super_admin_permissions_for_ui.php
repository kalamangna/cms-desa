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
        // Bersihkan cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Ambil peran super_admin
        $superAdmin = Role::where('name', 'super_admin')->first();
        
        if ($superAdmin) {
            // Berikan seluruh izin yang ada di database ke super_admin
            // Secara teknis super_admin sudah mem-bypass izin via Gate::before, 
            // tapi ini dilakukan agar UI Filament menampilkan angka "306" (bukan "0") 
            // sehingga tidak membingungkan pengguna.
            $superAdmin->syncPermissions(Permission::all());
        }

        // Bersihkan cache lagi
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
