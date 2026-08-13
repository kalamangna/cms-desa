<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Guard: Pada fresh install, kolom domicile_province/city/country tidak pernah dibuat di base CREATE.
        // dropColumn hanya dijalankan jika kolom tersebut benar-benar ada (dari existing install lama).
        Schema::table('citizens', function (Blueprint $table) {
            $colsToDrop = [];
            if (Schema::hasColumn('citizens', 'domicile_province')) {
                $colsToDrop[] = 'domicile_province';
            }
            if (Schema::hasColumn('citizens', 'domicile_city')) {
                $colsToDrop[] = 'domicile_city';
            }
            if (Schema::hasColumn('citizens', 'domicile_country')) {
                $colsToDrop[] = 'domicile_country';
            }
            if (! empty($colsToDrop)) {
                $table->dropColumn($colsToDrop);
            }
        });
    }

    public function down(): void
    {
        Schema::table('citizens', function (Blueprint $table) {
            $table->string('domicile_province')->nullable()->after('domicile_address_type');
            $table->string('domicile_city')->nullable()->after('domicile_province');
            $table->string('domicile_country')->nullable()->after('domicile_city');
        });
    }
};
