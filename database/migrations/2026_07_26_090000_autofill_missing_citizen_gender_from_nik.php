<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('citizens')) {
            // Otomatis lengkapi kolom gender yang NULL dari 16 digit NIK warga pada DB eksisting
            DB::table('citizens')
                ->whereNull('gender')
                ->orWhere('gender', '')
                ->orderBy('id')
                ->chunk(200, function ($citizens) {
                    foreach ($citizens as $citizen) {
                        if ($citizen->nik && strlen($citizen->nik) >= 12) {
                            $day = (int) substr($citizen->nik, 6, 2);
                            $gender = null;
                            if ($day > 40) {
                                $gender = 'Perempuan';
                            } elseif ($day >= 1 && $day <= 31) {
                                $gender = 'Laki-laki';
                            }

                            if ($gender) {
                                DB::table('citizens')
                                    ->where('id', $citizen->id)
                                    ->update(['gender' => $gender]);
                            }
                        }
                    }
                });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op untuk menjaga integritas data
    }
};
