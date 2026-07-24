<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('citizens', function (Blueprint $table) {
            $table->dropColumn(['domicile_province', 'domicile_city', 'domicile_country']);
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
