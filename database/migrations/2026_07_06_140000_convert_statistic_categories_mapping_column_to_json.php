<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $categories = DB::table('statistic_categories')->get();
        foreach ($categories as $cat) {
            if ($cat->mapping_column) {
                $val = trim($cat->mapping_column);
                if (!str_starts_with($val, '[')) {
                    $jsonVal = json_encode([$val]);
                    DB::table('statistic_categories')
                        ->where('id', $cat->id)
                        ->update(['mapping_column' => $jsonVal]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
