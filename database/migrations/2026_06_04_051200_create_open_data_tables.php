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
        if (Schema::hasTable('datasets')) {
            Schema::table('datasets', function (Blueprint $table) {
                if (!Schema::hasColumn('datasets', 'slug')) {
                    $table->string('slug')->unique()->nullable();
                }
                if (!Schema::hasColumn('datasets', 'description')) {
                    $table->text('description')->nullable();
                }
                if (!Schema::hasColumn('datasets', 'year')) {
                    $table->year('year')->nullable();
                }
                if (!Schema::hasColumn('datasets', 'source')) {
                    $table->string('source')->nullable();
                }
                if (!Schema::hasColumn('datasets', 'file_csv')) {
                    $table->string('file_csv')->nullable();
                }
                if (!Schema::hasColumn('datasets', 'file_xlsx')) {
                    $table->string('file_xlsx')->nullable();
                }
                if (!Schema::hasColumn('datasets', 'file_pdf')) {
                    $table->string('file_pdf')->nullable();
                }
                if (!Schema::hasColumn('datasets', 'deleted_at')) {
                    $table->softDeletes();
                }
            });
        } else {
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
        }

        // 2. Metadata Table
        if (Schema::hasTable('metadata')) {
            Schema::table('metadata', function (Blueprint $table) {
                if (!Schema::hasColumn('metadata', 'source')) {
                    $table->string('source')->nullable();
                }
                if (!Schema::hasColumn('metadata', 'definition')) {
                    $table->text('definition')->nullable();
                }
                if (!Schema::hasColumn('metadata', 'update_frequency')) {
                    $table->string('update_frequency')->nullable();
                }
                if (!Schema::hasColumn('metadata', 'responsible_person')) {
                    $table->string('responsible_person')->nullable();
                }
                if (!Schema::hasColumn('metadata', 'deleted_at')) {
                    $table->softDeletes();
                }
            });
        } else {
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
        }

        // 3. Publications Table (e.g. Infographics/Reports PDFs)
        if (Schema::hasTable('publications')) {
            Schema::table('publications', function (Blueprint $table) {
                if (!Schema::hasColumn('publications', 'slug')) {
                    $table->string('slug')->unique()->nullable();
                }
                if (!Schema::hasColumn('publications', 'type')) {
                    $table->string('type')->nullable();
                }
                if (!Schema::hasColumn('publications', 'year')) {
                    $table->year('year')->nullable();
                }
                if (!Schema::hasColumn('publications', 'cover')) {
                    $table->string('cover')->nullable();
                }
                if (!Schema::hasColumn('publications', 'pdf_file')) {
                    $table->string('pdf_file')->nullable();
                }
                if (!Schema::hasColumn('publications', 'deleted_at')) {
                    $table->softDeletes();
                }
            });
        } else {
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
