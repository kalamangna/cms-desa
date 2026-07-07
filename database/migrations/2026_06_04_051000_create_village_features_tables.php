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
        // 1. Announcements Table
        if (Schema::hasTable('announcements')) {
            Schema::table('announcements', function (Blueprint $table) {
                if (!Schema::hasColumn('announcements', 'deleted_at')) {
                    $table->softDeletes();
                }
            });
        } else {
            Schema::create('announcements', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('content');
                $table->string('photo')->nullable();
                $table->timestamp('published_at')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 2. Galleries Table
        if (Schema::hasTable('galleries')) {
            Schema::table('galleries', function (Blueprint $table) {
                if (!Schema::hasColumn('galleries', 'deleted_at')) {
                    $table->softDeletes();
                }
            });
        } else {
            Schema::create('galleries', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->string('image');
                $table->text('description')->nullable();
                $table->string('type')->default('image');
                $table->string('youtube_url')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 3. Documents Table
        if (Schema::hasTable('documents')) {
            Schema::table('documents', function (Blueprint $table) {
                if (!Schema::hasColumn('documents', 'deleted_at')) {
                    $table->softDeletes();
                }
            });
        } else {
            Schema::create('documents', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->string('file');
                $table->text('description')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 4. Services Table
        if (!Schema::hasTable('services')) {
            Schema::create('services', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->string('icon')->nullable();
                $table->text('description')->nullable();
                $table->text('requirements')->nullable();
                $table->timestamps();
            });
        }

        // 5. Institutions Table
        if (Schema::hasTable('institutions')) {
            Schema::table('institutions', function (Blueprint $table) {
                if (!Schema::hasColumn('institutions', 'deleted_at')) {
                    $table->softDeletes();
                }
            });
        } else {
            Schema::create('institutions', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('logo')->nullable();
                $table->text('description')->nullable();
                $table->string('motto')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 6. Officials Table
        if (Schema::hasTable('officials')) {
            Schema::table('officials', function (Blueprint $table) {
                if (!Schema::hasColumn('officials', 'deleted_at')) {
                    $table->softDeletes();
                }
            });
        } else {
            Schema::create('officials', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('position');
                $table->string('photo')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('officials');
        Schema::dropIfExists('institutions');
        Schema::dropIfExists('services');
        Schema::dropIfExists('documents');
        Schema::dropIfExists('galleries');
        Schema::dropIfExists('announcements');
    }
};
