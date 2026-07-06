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
        // 1. Datasets Table (Open Data)
        Schema::create('datasets', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->year('year');
            $table->string('source')->nullable();
            $table->string('file_csv')->nullable();
            $table->string('file_xlsx')->nullable();
            $table->string('file_pdf')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Metadata Table
        Schema::create('metadata', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dataset_id')->constrained()->cascadeOnDelete();
            $table->string('source')->nullable();
            $table->text('definition')->nullable();
            $table->string('update_frequency')->nullable();
            $table->string('responsible_person')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Publications Table (e.g. Infographics/Reports PDFs)
        Schema::create('publications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('type'); // Desa Dalam Angka, Profil Statistik, Infografis
            $table->year('year');
            $table->string('cover')->nullable();
            $table->string('pdf_file')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publications');
        Schema::dropIfExists('metadata');
        Schema::dropIfExists('datasets');
    }
};
