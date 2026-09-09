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
        if (Schema::hasTable('visitor_logs')) {
            $existing = collect(Schema::getIndexes('visitor_logs'))->pluck('name')->all();
            Schema::table('visitor_logs', function (Blueprint $table) use ($existing) {
                if (! in_array('visitor_logs_ip_hash_index', $existing, true)) {
                    $table->index('ip_hash');
                }
            });
        }

        if (Schema::hasTable('citizens')) {
            $existing = collect(Schema::getIndexes('citizens'))->pluck('name')->all();
            Schema::table('citizens', function (Blueprint $table) use ($existing) {
                if (! in_array('citizens_status_gender_index', $existing, true)) {
                    $table->index(['status', 'gender']);
                }
                if (! in_array('citizens_status_index', $existing, true)) {
                    $table->index('status');
                }
                if (! in_array('citizens_dusun_id_index', $existing, true)) {
                    $table->index('dusun_id');
                }
                if (! in_array('citizens_family_id_index', $existing, true)) {
                    $table->index('family_id');
                }
                if (! in_array('citizens_job_status_index', $existing, true)) {
                    $table->index('job_status');
                }
                if (! in_array('citizens_education_index', $existing, true)) {
                    $table->index('education');
                }
            });
        }

        if (Schema::hasTable('budget_realizations')) {
            $existing = collect(Schema::getIndexes('budget_realizations'))->pluck('name')->all();
            Schema::table('budget_realizations', function (Blueprint $table) use ($existing) {
                if (! in_array('budget_realizations_year_budget_category_id_index', $existing, true)) {
                    $table->index(['year', 'budget_category_id']);
                }
            });
        }

        if (Schema::hasTable('posts')) {
            $existing = collect(Schema::getIndexes('posts'))->pluck('name')->all();
            Schema::table('posts', function (Blueprint $table) use ($existing) {
                if (! in_array('posts_published_at_index', $existing, true)) {
                    $table->index('published_at');
                }
                if (! in_array('posts_category_id_index', $existing, true)) {
                    $table->index('category_id');
                }
            });
        }

        if (Schema::hasTable('announcements')) {
            $existing = collect(Schema::getIndexes('announcements'))->pluck('name')->all();
            Schema::table('announcements', function (Blueprint $table) use ($existing) {
                if (! in_array('announcements_published_at_index', $existing, true)) {
                    $table->index('published_at');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('announcements')) {
            Schema::table('announcements', function (Blueprint $table) {
                $table->dropIndex(['published_at']);
            });
        }

        if (Schema::hasTable('posts')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->dropIndex(['published_at']);
                $table->dropIndex(['category_id']);
            });
        }

        if (Schema::hasTable('budget_realizations')) {
            Schema::table('budget_realizations', function (Blueprint $table) {
                $table->dropIndex(['year', 'budget_category_id']);
            });
        }

        if (Schema::hasTable('citizens')) {
            Schema::table('citizens', function (Blueprint $table) {
                $table->dropIndex(['status', 'gender']);
                $table->dropIndex(['status']);
                $table->dropIndex(['dusun_id']);
                $table->dropIndex(['family_id']);
                $table->dropIndex(['job_status']);
                $table->dropIndex(['education']);
            });
        }

        if (Schema::hasTable('visitor_logs')) {
            Schema::table('visitor_logs', function (Blueprint $table) {
                $table->dropIndex(['ip_hash']);
            });
        }
    }
};
