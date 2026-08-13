<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class TelegramHelper
{
    /**
     * Get the village/app name from settings table.
     */
    public static function getAppName(): string
    {
        try {
            $name = DB::table('settings')->where('key', 'village_name')->value('value');

            return $name ? "Desa {$name}" : config('app.name', 'Website Desa');
        } catch (\Exception $e) {
            return config('app.name', 'Website Desa');
        }
    }

    /**
     * Build the standard footer line.
     */
    public static function footer(): string
    {
        $appName = self::getAppName();
        $date = now()->setTimezone('Asia/Makassar')->format('d M Y, H:i').' WITA';

        return "\n\n🏢 {$appName}\n<i>🕒 {$date}</i>";
    }
}
