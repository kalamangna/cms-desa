<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan kolom klasifikasi dokumen hukum dan informasi publik pada tabel documents.
     */
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->string('category', 50)->nullable()->after('description')->index();
            $table->string('information_classification', 30)->nullable()->after('category')->index();
            $table->string('document_number', 100)->nullable()->after('information_classification');
            $table->unsignedSmallInteger('year')->nullable()->after('document_number')->index();
            $table->date('published_date')->nullable()->after('year');
        });
    }

    /**
     * Menghapus kolom dan indeks klasifikasi dokumen.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropIndex(['category']);
            $table->dropIndex(['information_classification']);
            $table->dropIndex(['year']);
            $table->dropColumn([
                'category',
                'information_classification',
                'document_number',
                'year',
                'published_date',
            ]);
        });
    }
};
