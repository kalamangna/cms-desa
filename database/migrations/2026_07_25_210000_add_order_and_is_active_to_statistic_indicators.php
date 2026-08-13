<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add missing columns to statistic_indicators:
     * - order: urutan tampil indikator dalam kategori
     * - is_active: toggle tampil/sembunyikan indikator
     *
     * Fix untuk BUG-01: Data migration sebelumnya sudah menggunakan kolom ini
     * namun kolom belum didefinisikan di schema, sehingga data dibuang diam-diam
     * oleh mass assignment guard Laravel.
     */
    public function up(): void
    {
        if (! Schema::hasTable('statistic_indicators')) {
            return;
        }

        Schema::table('statistic_indicators', function (Blueprint $table) {
            if (! Schema::hasColumn('statistic_indicators', 'order')) {
                $table->integer('order')->default(0)->after('mapping_value');
            }
            if (! Schema::hasColumn('statistic_indicators', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('order');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('statistic_indicators')) {
            return;
        }

        Schema::table('statistic_indicators', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('statistic_indicators', 'is_active')) {
                $cols[] = 'is_active';
            }
            if (Schema::hasColumn('statistic_indicators', 'order')) {
                $cols[] = 'order';
            }
            if (! empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
