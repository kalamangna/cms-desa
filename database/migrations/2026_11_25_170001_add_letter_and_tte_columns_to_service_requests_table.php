<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->json('form_data')->nullable()->after('admin_response');
            $table->json('letter_payload')->nullable()->after('form_data');
            $table->string('letter_number', 120)->nullable()->index()->after('letter_payload');
            $table->date('letter_date')->nullable()->after('letter_number');
            $table->string('draft_pdf_path', 255)->nullable()->after('letter_date');
            $table->string('signed_pdf_path', 255)->nullable()->after('draft_pdf_path');
            $table->string('signed_pdf_hash', 64)->nullable()->after('signed_pdf_path');
            $table->string('verification_token', 64)->nullable()->unique()->after('signed_pdf_hash');
            $table->string('tte_status', 20)->default('none')->index()->after('verification_token');
            $table->foreignId('signer_official_id')->nullable()->after('tte_status')->constrained('officials')->nullOnDelete();
            $table->string('signer_name', 150)->nullable()->after('signer_official_id');
            $table->string('signer_position', 150)->nullable()->after('signer_name');
            $table->string('signer_nik_masked', 32)->nullable()->after('signer_position');
            $table->timestamp('signed_at')->nullable()->after('signer_nik_masked');
            $table->foreignId('signed_by_user_id')->nullable()->after('signed_at')->constrained('users')->nullOnDelete();
            $table->timestamp('revoked_at')->nullable()->after('signed_by_user_id');
            $table->foreignId('revoked_by_user_id')->nullable()->after('revoked_at')->constrained('users')->nullOnDelete();
            $table->text('revocation_reason')->nullable()->after('revoked_by_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropForeign(['signer_official_id']);
            $table->dropForeign(['signed_by_user_id']);
            $table->dropForeign(['revoked_by_user_id']);

            $table->dropColumn([
                'form_data',
                'letter_payload',
                'letter_number',
                'letter_date',
                'draft_pdf_path',
                'signed_pdf_path',
                'signed_pdf_hash',
                'verification_token',
                'tte_status',
                'signer_official_id',
                'signer_name',
                'signer_position',
                'signer_nik_masked',
                'signed_at',
                'signed_by_user_id',
                'revoked_at',
                'revoked_by_user_id',
                'revocation_reason',
            ]);
        });
    }
};
