<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('institutions')) {
            Schema::table('institutions', function (Blueprint $table) {
                if (Schema::hasColumn('institutions', 'motto')) {
                    $table->dropColumn('motto');
                }
                if (! Schema::hasColumn('institutions', 'management')) {
                    $table->json('management')->nullable()->after('description');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('institutions')) {
            Schema::table('institutions', function (Blueprint $table) {
                if (! Schema::hasColumn('institutions', 'motto')) {
                    $table->string('motto')->nullable()->after('description');
                }
                if (Schema::hasColumn('institutions', 'management')) {
                    $table->dropColumn('management');
                }
            });
        }
    }
};
