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
        // 1. Create guest_books, complaints, service_requests
        if (!Schema::hasTable('guest_books')) {
            Schema::create('guest_books', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('phone');
                $table->string('institution_address')->nullable();
                $table->text('purpose');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('complaints')) {
            Schema::create('complaints', function (Blueprint $table) {
                $table->id();
                $table->string('ticket_number')->unique();
                $table->string('name');
                $table->string('phone');
                $table->string('title');
                $table->text('content');
                $table->string('status')->default('Pending'); // Pending, Proses, Selesai
                $table->text('response')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('service_requests')) {
            Schema::create('service_requests', function (Blueprint $table) {
                $table->id();
                $table->string('ticket_number')->unique();
                $table->string('nik', 16);
                $table->string('name');
                $table->string('phone');
                $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
                $table->string('status')->default('Menunggu'); // Menunggu, Proses, Selesai, Ditolak
                $table->text('admin_response')->nullable();
                $table->timestamps();
            });
        }

        // 2. Update dusuns, officials, institutions
        if (Schema::hasTable('dusuns')) {
            Schema::table('dusuns', function (Blueprint $table) {
                if (!Schema::hasColumn('dusuns', 'total_rt')) {
                    $table->integer('total_rt')->default(0)->after('head_name');
                }
                if (!Schema::hasColumn('dusuns', 'total_rw')) {
                    $table->integer('total_rw')->default(0)->after('total_rt');
                }
            });
        }

        if (!Schema::hasTable('public_facilities')) {
            Schema::create('public_facilities', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('type'); // Pendidikan, Ibadah, Kesehatan, Pemerintahan, dll.
                $table->double('latitude');
                $table->double('longitude');
                $table->text('address')->nullable();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('officials')) {
            Schema::table('officials', function (Blueprint $table) {
                if (!Schema::hasColumn('officials', 'parent_id')) {
                    $table->foreignId('parent_id')->nullable()->constrained('officials')->nullOnDelete()->after('id');
                }
                if (!Schema::hasColumn('officials', 'level')) {
                    $table->integer('level')->default(4)->after('position'); // 1 = Kades, 2 = Sekdes, 3 = Kasi/Kaur, 4 = Kadus/Lainnya
                }
                if (!Schema::hasColumn('officials', 'order')) {
                    $table->integer('order')->default(0)->after('level');
                }
            });
        }

        if (Schema::hasTable('institutions')) {
            Schema::table('institutions', function (Blueprint $table) {
                if (!Schema::hasColumn('institutions', 'slug')) {
                    $table->string('slug')->nullable()->after('name');
                }
            });
        }

        // 3. Update statistic_data, statistic_categories, create popup_infographics
        if (Schema::hasTable('statistic_data')) {
            Schema::table('statistic_data', function (Blueprint $table) {
                if (!Schema::hasColumn('statistic_data', 'value_male')) {
                    $table->integer('value_male')->default(0)->after('value');
                }
                if (!Schema::hasColumn('statistic_data', 'value_female')) {
                    $table->integer('value_female')->default(0)->after('value_male');
                }
            });
        }

        if (!Schema::hasTable('popup_infographics')) {
            Schema::create('popup_infographics', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->string('image');
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (Schema::hasTable('statistic_categories')) {
            Schema::table('statistic_categories', function (Blueprint $table) {
                if (!Schema::hasColumn('statistic_categories', 'comparison_column')) {
                    $table->string('comparison_column')->nullable();
                }
                if (!Schema::hasColumn('statistic_categories', 'comparison_value_a')) {
                    $table->string('comparison_value_a')->nullable();
                }
                if (!Schema::hasColumn('statistic_categories', 'comparison_value_b')) {
                    $table->string('comparison_value_b')->nullable();
                }
                if (!Schema::hasColumn('statistic_categories', 'comparison_label_a')) {
                    $table->string('comparison_label_a')->nullable();
                }
                if (!Schema::hasColumn('statistic_categories', 'comparison_label_b')) {
                    $table->string('comparison_label_b')->nullable();
                }
            });
        }

        // 4. Create village_potentials
        if (!Schema::hasTable('village_potentials')) {
            Schema::create('village_potentials', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->string('category'); // Pariwisata, Pertanian, Peternakan, dll.
                $table->text('description')->nullable();
                $table->string('image')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 4. Drop village_potentials
        Schema::dropIfExists('village_potentials');

        // 3. Update statistic_categories, popup_infographics, statistic_data
        if (Schema::hasTable('statistic_categories')) {
            Schema::table('statistic_categories', function (Blueprint $table) {
                $cols = [];
                if (Schema::hasColumn('statistic_categories', 'comparison_column')) $cols[] = 'comparison_column';
                if (Schema::hasColumn('statistic_categories', 'comparison_value_a')) $cols[] = 'comparison_value_a';
                if (Schema::hasColumn('statistic_categories', 'comparison_value_b')) $cols[] = 'comparison_value_b';
                if (Schema::hasColumn('statistic_categories', 'comparison_label_a')) $cols[] = 'comparison_label_a';
                if (Schema::hasColumn('statistic_categories', 'comparison_label_b')) $cols[] = 'comparison_label_b';
                if (!empty($cols)) {
                    $table->dropColumn($cols);
                }
            });
        }

        Schema::dropIfExists('popup_infographics');

        if (Schema::hasTable('statistic_data')) {
            Schema::table('statistic_data', function (Blueprint $table) {
                $cols = [];
                if (Schema::hasColumn('statistic_data', 'value_male')) $cols[] = 'value_male';
                if (Schema::hasColumn('statistic_data', 'value_female')) $cols[] = 'value_female';
                if (!empty($cols)) {
                    $table->dropColumn($cols);
                }
            });
        }

        // 2. Update institutions, officials, public_facilities, dusuns
        if (Schema::hasTable('institutions')) {
            Schema::table('institutions', function (Blueprint $table) {
                if (Schema::hasColumn('institutions', 'slug')) {
                    $table->dropColumn('slug');
                }
            });
        }

        if (Schema::hasTable('officials')) {
            Schema::table('officials', function (Blueprint $table) {
                if (Schema::hasColumn('officials', 'parent_id')) {
                    try {
                        $table->dropForeign(['parent_id']);
                    } catch (\Exception $e) {}
                    $table->dropColumn('parent_id');
                }
                $cols = [];
                if (Schema::hasColumn('officials', 'level')) $cols[] = 'level';
                if (Schema::hasColumn('officials', 'order')) $cols[] = 'order';
                if (!empty($cols)) {
                    $table->dropColumn($cols);
                }
            });
        }

        Schema::dropIfExists('public_facilities');

        if (Schema::hasTable('dusuns')) {
            Schema::table('dusuns', function (Blueprint $table) {
                $cols = [];
                if (Schema::hasColumn('dusuns', 'total_rt')) $cols[] = 'total_rt';
                if (Schema::hasColumn('dusuns', 'total_rw')) $cols[] = 'total_rw';
                if (!empty($cols)) {
                    $table->dropColumn($cols);
                }
            });
        }

        // 1. Drop service_requests, complaints, guest_books
        Schema::dropIfExists('service_requests');
        Schema::dropIfExists('complaints');
        Schema::dropIfExists('guest_books');
    }
};
