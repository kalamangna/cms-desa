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
            $table->string('assistance_type')->nullable(); // bantuan sosial
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('families');
    }
};
