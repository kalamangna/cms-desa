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
        // 1. Categories Table (News Categories)
        if (Schema::hasTable('categories')) {
            Schema::table('categories', function (Blueprint $table) {
                if (!Schema::hasColumn('categories', 'deleted_at')) {
                    $table->softDeletes();
                }
            });
        } else {
            Schema::create('categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 2. Posts Table (News Articles)
        if (Schema::hasTable('posts')) {
            Schema::table('posts', function (Blueprint $table) {
                if (!Schema::hasColumn('posts', 'deleted_at')) {
                    $table->softDeletes();
                }
                if (Schema::hasColumn('posts', 'photo') && !Schema::hasColumn('posts', 'featured_image')) {
                    $table->renameColumn('photo', 'featured_image');
                } elseif (!Schema::hasColumn('posts', 'featured_image')) {
                    $table->string('featured_image')->nullable();
                }
            });
        } else {
            Schema::create('posts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('category_id')->constrained()->cascadeOnDelete();
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('content');
                $table->string('featured_image')->nullable();
                $table->timestamp('published_at')->nullable();
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
        Schema::dropIfExists('posts');
        Schema::dropIfExists('categories');
    }
};
