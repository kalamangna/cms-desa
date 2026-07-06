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
        // 1. Budget Categories Table (APBDes Categories)
        Schema::create('budget_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        // 2. Budget Realizations Table
        Schema::create('budget_realizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_category_id')->constrained()->cascadeOnDelete();
            $table->integer('year');
            $table->bigInteger('budget_amount');
            $table->bigInteger('realization_amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_realizations');
        Schema::dropIfExists('budget_categories');
    }
};
