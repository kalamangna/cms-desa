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
        Schema::table('citizens', function (Blueprint $table) {
            $table->foreignId('family_id')->nullable()->constrained('families')->nullOnDelete()->after('dusun_id');
            $table->integer('kk_order')->nullable()->after('family_id');
            $table->string('family_relation')->nullable()->after('marital_status');
            $table->string('citizenship_status')->nullable()->after('status');
            
            // Pendidikan & Kesehatan
            $table->string('school_participation')->nullable()->after('family_relation');
            $table->string('education_level')->nullable()->after('school_participation');
            $table->string('bpjs_status')->nullable()->after('education_level');
            $table->string('pip_status')->nullable()->after('bpjs_status');
            
            // Pekerjaan & Pendapatan
            $table->string('has_income')->nullable()->after('job');
            $table->string('job_status')->nullable()->after('has_income');
            $table->bigInteger('income_salary')->default(0)->after('job_status');
            $table->bigInteger('income_allowance')->default(0)->after('income_salary');
            $table->bigInteger('income_food')->default(0)->after('income_allowance');
            $table->bigInteger('income_honor')->default(0)->after('income_food');
            $table->bigInteger('income_overtime')->default(0)->after('income_honor');
            $table->bigInteger('income_other')->default(0)->after('income_overtime');
            $table->bigInteger('income_business')->default(0)->after('income_other');
            $table->bigInteger('income_passive')->default(0)->after('income_business');
            
            // Disabilitas
            $table->string('disability_physical')->nullable()->after('income_passive');
            $table->string('disability_mental')->nullable()->after('disability_physical');
            $table->string('disability_intellectual')->nullable()->after('disability_mental');
            $table->string('disability_blind')->nullable()->after('disability_intellectual');
            $table->string('disability_deaf')->nullable()->after('disability_blind');
            $table->string('disability_speech')->nullable()->after('disability_deaf');
            
            // Penyakit Kronis
            $table->string('illness_hypertension')->nullable()->after('disability_speech');
            $table->string('illness_rheumatic')->nullable()->after('illness_hypertension');
            $table->string('illness_asthma')->nullable()->after('illness_rheumatic');
            $table->string('illness_heart')->nullable()->after('illness_asthma');
            $table->string('illness_diabetes')->nullable()->after('illness_heart');
            $table->string('illness_tbc')->nullable()->after('illness_diabetes');
            $table->string('illness_stroke')->nullable()->after('illness_tbc');
            $table->string('illness_cancer')->nullable()->after('illness_stroke');
            $table->string('illness_kidney')->nullable()->after('illness_cancer');
            $table->string('illness_hemophilia')->nullable()->after('illness_kidney');
            $table->string('illness_hiv')->nullable()->after('illness_hemophilia');
            $table->string('illness_cholesterol')->nullable()->after('illness_hiv');
            $table->string('illness_liver')->nullable()->after('illness_cholesterol');
            $table->string('illness_thalassemia')->nullable()->after('illness_liver');
            $table->string('illness_leukemia')->nullable()->after('illness_thalassemia');
            $table->string('illness_alzheimer')->nullable()->after('illness_leukemia');
            $table->string('illness_other')->nullable()->after('illness_alzheimer');
            
            // Rekening & Dompet Digital
            $table->string('has_digital_wallet')->nullable()->after('illness_other');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('citizens', function (Blueprint $table) {
            $table->dropForeign(['family_id']);
            $table->dropColumn([
                'family_id', 'kk_order', 'family_relation', 'citizenship_status',
                'school_participation', 'education_level', 'bpjs_status', 'pip_status',
                'has_income', 'job_status', 'income_salary', 'income_allowance',
                'income_food', 'income_honor', 'income_overtime', 'income_other',
                'income_business', 'income_passive',
                'disability_physical', 'disability_mental', 'disability_intellectual',
                'disability_blind', 'disability_deaf', 'disability_speech',
                'illness_hypertension', 'illness_rheumatic', 'illness_asthma',
                'illness_heart', 'illness_diabetes', 'illness_tbc', 'illness_stroke',
                'illness_cancer', 'illness_kidney', 'illness_hemophilia', 'illness_hiv',
                'illness_cholesterol', 'illness_liver', 'illness_thalassemia',
                'illness_leukemia', 'illness_alzheimer', 'illness_other',
                'has_digital_wallet'
            ]);
        });
    }
};
