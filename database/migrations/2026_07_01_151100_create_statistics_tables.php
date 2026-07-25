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
        // 1. Statistic Categories Table
        if (!Schema::hasTable('statistic_categories')) {
            Schema::create('statistic_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->string('mapping_table')->nullable();
                $table->text('mapping_column')->nullable();
                $table->json('secondary_columns')->nullable();   // Kolom sekunder (e.g. ['gender'])
                $table->string('comparison_column')->nullable();  // Kolom untuk perbandingan
                $table->string('comparison_value_a')->nullable();
                $table->string('comparison_value_b')->nullable();
                $table->string('comparison_label_a')->nullable();
                $table->string('comparison_label_b')->nullable();
                $table->timestamps();
            });
        }

        // 2. Statistic Indicators Table
        if (!Schema::hasTable('statistic_indicators')) {
            Schema::create('statistic_indicators', function (Blueprint $table) {
                $table->id();
                $table->foreignId('statistic_category_id')->constrained()->cascadeOnDelete();
                $table->string('name');
                $table->string('unit')->nullable();
                $table->string('mapping_column')->nullable();
                $table->string('mapping_operator')->default('=');
                $table->string('mapping_value')->nullable();
                $table->timestamps();
            });
        }

        // 3. Statistic Data Table
        if (!Schema::hasTable('statistic_data')) {
            Schema::create('statistic_data', function (Blueprint $table) {
                $table->id();
                $table->foreignId('statistic_indicator_id')->constrained()->cascadeOnDelete();
                $table->integer('year');
                $table->integer('value');
                $table->integer('value_male')->default(0);   // Nilai laki-laki
                $table->integer('value_female')->default(0); // Nilai perempuan
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statistic_data');
        Schema::dropIfExists('statistic_indicators');
        Schema::dropIfExists('statistic_categories');
    }
};
