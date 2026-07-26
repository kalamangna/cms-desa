<?php

namespace App\Notifications\Backup;

use Spatie\Backup\Notifications\Notifications\BackupHasFailedNotification as BaseNotification;

class BackupHasFailedNotification extends BaseNotification
{
    use HasTelegramNotification;
}
