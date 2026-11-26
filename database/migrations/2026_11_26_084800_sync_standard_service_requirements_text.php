<?php

use App\Services\ServiceStandardizationService;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        if (app()->runningUnitTests()) {
            return;
        }

        app(ServiceStandardizationService::class)->standardize();
    }

    public function down(): void
    {
        // Sinkronisasi teks persyaratan tidak di-rollback otomatis.
    }
};
