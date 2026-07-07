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
        if (!Schema::hasTable('visitor_logs')) {
            Schema::create('visitor_logs', function (Blueprint $blueprint) {
                $blueprint->id();
                $blueprint->string('ip_hash', 64);
                $blueprint->string('url')->nullable();
                $blueprint->string('user_agent')->nullable();
                $blueprint->date('visit_date');
                $blueprint->timestamps();

                // Indexes for speed
                $blueprint->index(['visit_date', 'ip_hash']);
                $blueprint->index('visit_date');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitor_logs');
    }
};
