<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('letter_code', 30)->nullable()->after('requirements');
            $table->string('letter_title', 180)->nullable()->after('letter_code');
            $table->longText('letter_template')->nullable()->after('letter_title');
            $table->json('form_fields')->nullable()->after('letter_template');
            $table->boolean('is_tte_enabled')->default(false)->after('form_fields');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn([
                'letter_code',
                'letter_title',
                'letter_template',
                'form_fields',
                'is_tte_enabled',
            ]);
        });
    }
};
