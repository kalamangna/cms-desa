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
        // 1. Dusuns Table
        if (!Schema::hasTable('dusuns')) {
            Schema::create('dusuns', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('head_name')->nullable();
                $table->text('geojson')->nullable();
                $table->timestamps();
            });
        }

        // 2. Families Table (Keluarga)
        if (Schema::hasTable('families')) {
            Schema::table('families', function (Blueprint $table) {
                if (!Schema::hasColumn('families', 'deleted_at')) {
                    $table->softDeletes();
                }
            });
        } else {
            Schema::create('families', function (Blueprint $table) {
                $table->id();
                $table->string('kk_number')->unique()->index();
                $table->string('head_name')->nullable();
                $table->string('head_nik')->nullable();
                $table->text('address')->nullable();
                $table->foreignId('dusun_id')->nullable()->constrained('dusuns')->nullOnDelete();
                $table->string('rt', 10)->nullable();
                $table->string('rw', 10)->nullable();
                $table->boolean('address_matches_kk')->default(false);
                $table->string('assistance_type')->nullable(); // Bantuan sosial
                $table->integer('family_member_count')->default(1);
                
                // Karakteristik Bangunan
                $table->string('building_type')->nullable();
                $table->string('ownership_status')->nullable();
                $table->string('ownership_proof')->nullable();
                $table->float('floor_area')->nullable();
                $table->string('floor_material')->nullable();
                $table->string('wall_material')->nullable();
                $table->string('roof_material')->nullable();
                $table->string('floor_condition')->nullable();
                $table->string('wall_condition')->nullable();
                $table->string('roof_condition')->nullable();
                
                // Sanitasi & Listrik
                $table->string('toilet_facility')->nullable();
                $table->string('closet_type')->nullable();
                $table->string('feces_disposal')->nullable();
                $table->string('water_source')->nullable();
                $table->string('lighting_source')->nullable();
                $table->string('electricity_power')->nullable();
                $table->string('electricity_id')->nullable();
                $table->bigInteger('electricity_cost')->nullable();
                $table->bigInteger('internet_cost')->nullable();
                
                // Foto
                $table->string('photo_front')->nullable();
                $table->string('photo_living_room')->nullable();
                $table->string('photo_bathroom')->nullable();
                $table->string('photo_kk')->nullable();
                
                // Aset
                $table->integer('gas_3kg_count')->default(0);
                $table->integer('gas_5kg_count')->default(0);
                $table->integer('refrigerator_count')->default(0);
                $table->integer('ac_count')->default(0);
                $table->integer('jewelry_count')->default(0);
                $table->integer('computer_count')->default(0);
                $table->integer('motorcycle_count')->default(0);
                $table->bigInteger('motorcycle_value')->default(0);
                $table->integer('car_count')->default(0);
                $table->bigInteger('car_value')->default(0);
                $table->integer('other_land_count')->default(0);
                $table->bigInteger('other_land_value')->default(0);
                $table->integer('other_building_count')->default(0);
                $table->bigInteger('other_building_value')->default(0);
                
                $table->text('notes')->nullable();
                
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 3. Citizens Table (Penduduk)
        if (Schema::hasTable('citizens')) {
            Schema::table('citizens', function (Blueprint $table) {
                if (!Schema::hasColumn('citizens', 'deleted_at')) {
                    $table->softDeletes();
                }
            });
        } else {
            Schema::create('citizens', function (Blueprint $table) {
                $table->id();
                $table->string('nik', 16)->unique();
                $table->string('name');
                $table->string('place_of_birth')->nullable();
                $table->date('date_of_birth')->nullable();
                $table->enum('gender', ['Laki-laki', 'Perempuan'])->nullable();
                $table->string('religion')->nullable();
                $table->string('education')->nullable();
                $table->string('job')->nullable();
                $table->string('blood_type')->nullable();
                $table->string('marital_status')->nullable();
                $table->text('address')->nullable();
                $table->string('rt', 3)->nullable();
                $table->string('rw', 3)->nullable();
                $table->string('status')->default('Aktif'); // Aktif, Pindah, Meninggal
                
                // Relasi
                $table->foreignId('dusun_id')->nullable()->constrained('dusuns')->nullOnDelete();
                $table->foreignId('family_id')->nullable()->constrained('families')->nullOnDelete();
                $table->integer('kk_order')->nullable();
                $table->string('family_relation')->nullable();
                $table->string('citizenship_status')->nullable();
                
                // Pendidikan & Kesehatan
                $table->string('school_participation')->nullable();
                $table->string('education_level')->nullable();
                $table->string('bpjs_status')->nullable();
                $table->boolean('pip_status')->default(false);
                
                // Pekerjaan & Pendapatan
                $table->boolean('has_income')->default(false);
                $table->string('job_status')->nullable();
                $table->bigInteger('income_salary')->default(0);
                $table->bigInteger('income_allowance')->default(0);
                $table->bigInteger('income_food')->default(0);
                $table->bigInteger('income_honor')->default(0);
                $table->bigInteger('income_overtime')->default(0);
                $table->bigInteger('income_other')->default(0);
                $table->bigInteger('income_business')->default(0);
                $table->bigInteger('income_passive')->default(0);
                
                // Disabilitas (Boolean)
                $table->boolean('disability_physical')->default(false);
                $table->boolean('disability_mental')->default(false);
                $table->boolean('disability_intellectual')->default(false);
                $table->boolean('disability_blind')->default(false);
                $table->boolean('disability_deaf')->default(false);
                $table->boolean('disability_speech')->default(false);
                
                // Penyakit Kronis (Boolean)
                $table->boolean('illness_hypertension')->default(false);
                $table->boolean('illness_rheumatic')->default(false);
                $table->boolean('illness_asthma')->default(false);
                $table->boolean('illness_heart')->default(false);
                $table->boolean('illness_diabetes')->default(false);
                $table->boolean('illness_tbc')->default(false);
                $table->boolean('illness_stroke')->default(false);
                $table->boolean('illness_cancer')->default(false);
                $table->boolean('illness_kidney')->default(false);
                $table->boolean('illness_hemophilia')->default(false);
                $table->boolean('illness_hiv')->default(false);
                $table->boolean('illness_cholesterol')->default(false);
                $table->boolean('illness_liver')->default(false);
                $table->boolean('illness_thalassemia')->default(false);
                $table->boolean('illness_leukemia')->default(false);
                $table->boolean('illness_alzheimer')->default(false);
                $table->boolean('illness_other')->default(false);
                
                // Rekening & Dompet Digital
                $table->boolean('has_digital_wallet')->default(false);
                
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
        Schema::dropIfExists('citizens');
        Schema::dropIfExists('families');
        Schema::dropIfExists('dusuns');
    }
};
