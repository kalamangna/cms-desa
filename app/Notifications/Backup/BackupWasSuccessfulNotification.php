<?php

namespace App\Notifications\Backup;

use Spatie\Backup\Notifications\Notifications\BackupWasSuccessfulNotification as BaseNotification;

class BackupWasSuccessfulNotification extends BaseNotification
{
    use HasTelegramNotification;
}
