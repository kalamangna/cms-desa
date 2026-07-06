<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $columns = [
            'bpjs_status', 'pip_status', 'has_income', 'has_digital_wallet',
            'disability_physical', 'disability_mental', 'disability_intellectual',
            'disability_blind', 'disability_deaf', 'disability_speech',
            'illness_hypertension', 'illness_rheumatic', 'illness_asthma',
            'illness_heart', 'illness_diabetes', 'illness_tbc', 'illness_stroke',
            'illness_cancer', 'illness_kidney', 'illness_hemophilia', 'illness_hiv',
            'illness_cholesterol', 'illness_liver', 'illness_thalassemia',
            'illness_leukemia', 'illness_alzheimer', 'illness_other'
        ];

        // 1. Convert existing data to 1/0
        foreach ($columns as $column) {
            DB::table('citizens')->where($column, 'Ya')->update([$column => '1']);
            DB::table('citizens')->where($column, 'Tidak')->update([$column => '0']);
            DB::table('citizens')->whereNull($column)->update([$column => '0']);
        }

        DB::table('families')->where('address_matches_kk', 'Ya')->update(['address_matches_kk' => '1']);
        DB::table('families')->where('address_matches_kk', 'Tidak')->update(['address_matches_kk' => '0']);
        DB::table('families')->whereNull('address_matches_kk')->update(['address_matches_kk' => '0']);

        // 2. Change column types to boolean
        Schema::table('citizens', function (Blueprint $table) use ($columns) {
            foreach ($columns as $column) {
                $table->boolean($column)->default(false)->change();
            }
        });

        Schema::table('families', function (Blueprint $table) {
            $table->boolean('address_matches_kk')->default(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $columns = [
            'bpjs_status', 'pip_status', 'has_income', 'has_digital_wallet',
            'disability_physical', 'disability_mental', 'disability_intellectual',
            'disability_blind', 'disability_deaf', 'disability_speech',
            'illness_hypertension', 'illness_rheumatic', 'illness_asthma',
            'illness_heart', 'illness_diabetes', 'illness_tbc', 'illness_stroke',
            'illness_cancer', 'illness_kidney', 'illness_hemophilia', 'illness_hiv',
            'illness_cholesterol', 'illness_liver', 'illness_thalassemia',
            'illness_leukemia', 'illness_alzheimer', 'illness_other'
        ];

        Schema::table('citizens', function (Blueprint $table) use ($columns) {
            foreach ($columns as $column) {
                $table->string($column)->nullable()->change();
            }
        });

        Schema::table('families', function (Blueprint $table) {
            $table->string('address_matches_kk')->nullable()->change();
        });

        foreach ($columns as $column) {
            DB::table('citizens')->where($column, '1')->update([$column => 'Ya']);
            DB::table('citizens')->where($column, '0')->update([$column => 'Tidak']);
        }

        DB::table('families')->where('address_matches_kk', '1')->update(['address_matches_kk' => 'Ya']);
        DB::table('families')->where('address_matches_kk', '0')->update(['address_matches_kk' => 'Tidak']);
    }
};
