<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\StatisticCategory;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('statistic_categories')) {
            Schema::table('statistic_categories', function (Blueprint $table) {
                if (! Schema::hasColumn('statistic_categories', 'secondary_columns')) {
                    $table->json('secondary_columns')->nullable()->after('mapping_column');
                }
            });

            // Set default secondary_columns (gender untuk citizens, kosong untuk families)
            StatisticCategory::where('mapping_table', 'citizens')
                ->update([
                    'secondary_columns' => json_encode(['gender']),
                ]);

            StatisticCategory::where('mapping_table', 'families')
                ->update([
                    'secondary_columns' => json_encode([]),
                ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('statistic_categories')) {
            Schema::table('statistic_categories', function (Blueprint $table) {
                if (Schema::hasColumn('statistic_categories', 'secondary_columns')) {
                    $table->dropColumn('secondary_columns');
                }
            });
        }
    }
};
