<?php

namespace App\Services\Backup;

use Spatie\Backup\BackupDestination\BackupCollection;
use Spatie\Backup\Tasks\Cleanup\Strategies\DefaultStrategy;

class CustomCleanupStrategy extends DefaultStrategy
{
    /** @var array<string, float> */
    public static array $freedStorage = [];

    /** @var array<string, int> */
    public static array $deletedCounts = [];

    public function deleteOldBackups(BackupCollection $backups): void
    {
        $diskName = $this->backupDestination->diskName();
        $sizeBefore = (float) $backups->size();
        $countBefore = (int) $backups->count();

        parent::deleteOldBackups($backups);

        $sizeAfter = (float) $this->backupDestination->fresh()->usedStorage();
        $countAfter = (int) $this->backupDestination->backups()->count();

        self::$freedStorage[$diskName] = max(0, $sizeBefore - $sizeAfter);
        self::$deletedCounts[$diskName] = max(0, $countBefore - $countAfter);
    }
}
