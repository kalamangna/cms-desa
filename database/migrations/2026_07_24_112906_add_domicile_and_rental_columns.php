<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add domicile address columns to citizens table:
     * - domicile_address_type: nilai enum dari kolom 304 (Sesuai KK dan KTP, Hanya sesuai KK, Hanya sesuai KTP)
     * - domicile_province: provinsi domisili dari kolom 305DN.a
     * - domicile_city: kabupaten/kota domisili dari kolom 305DN.b
     * - domicile_country: negara domisili dari kolom 305LN
     */
    public function up(): void
    {
        Schema::table('citizens', function (Blueprint $table) {
            if (!Schema::hasColumn('citizens', 'domicile_address_type')) {
                $table->string('domicile_address_type')->nullable()->after('citizenship_status');
            }
            if (!Schema::hasColumn('citizens', 'domicile_province')) {
                $table->string('domicile_province')->nullable()->after('domicile_address_type');
            }
            if (!Schema::hasColumn('citizens', 'domicile_city')) {
                $table->string('domicile_city')->nullable()->after('domicile_province');
            }
            if (!Schema::hasColumn('citizens', 'domicile_country')) {
                $table->string('domicile_country')->nullable()->after('domicile_city');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('citizens', function (Blueprint $table) {
            $table->dropColumn(['domicile_address_type', 'domicile_province', 'domicile_city', 'domicile_country']);
        });
    }
};
