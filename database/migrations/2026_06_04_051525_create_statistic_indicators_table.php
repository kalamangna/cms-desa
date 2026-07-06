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
        Schema::create('statistic_indicators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('statistic_category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('unit')->nullable(); // e.g., Orang, Jiwa, Persen
            $table->string('mapping_column')->nullable();
            $table->string('mapping_operator')->default('=');
            $table->string('mapping_value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statistic_indicators');
    }
};
