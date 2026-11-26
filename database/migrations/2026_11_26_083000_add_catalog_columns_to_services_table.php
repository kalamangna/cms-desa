<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('category', 60)->default('Layanan Lainnya')->after('slug');
            $table->unsignedSmallInteger('sort_order')->default(100)->after('category');
            $table->boolean('is_active')->default(true)->index()->after('is_tte_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['category', 'sort_order', 'is_active']);
        });
    }
};
