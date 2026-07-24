<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add rental/ownership cost columns to families table from Excel kolom 203:
     * - rental_estimate: 203.a. Perkiraan sewa sebulan (jika sewa/kontrak)
     * - rental_free_estimate: 203.b. Perkiraan sewa sebulan (jika bebas sewa/dinas/lainnya)
     * - rental_contract_value: 203.c. Nilai kontrak (jika kontrak/sewa)
     */
    public function up(): void
    {
        Schema::table('families', function (Blueprint $table) {
            if (!Schema::hasColumn('families', 'rental_estimate')) {
                $table->bigInteger('rental_estimate')->nullable()->after('ownership_proof');
            }
            if (!Schema::hasColumn('families', 'rental_free_estimate')) {
                $table->bigInteger('rental_free_estimate')->nullable()->after('rental_estimate');
            }
            if (!Schema::hasColumn('families', 'rental_contract_value')) {
                $table->bigInteger('rental_contract_value')->nullable()->after('rental_free_estimate');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('families', function (Blueprint $table) {
            $table->dropColumn(['rental_estimate', 'rental_free_estimate', 'rental_contract_value']);
        });
    }
};
