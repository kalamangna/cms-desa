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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citizens');
    }
};
