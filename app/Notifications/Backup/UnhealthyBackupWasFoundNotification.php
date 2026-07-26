<?php

namespace App\Notifications\Backup;

use Spatie\Backup\Notifications\Notifications\UnhealthyBackupWasFoundNotification as BaseNotification;

class UnhealthyBackupWasFoundNotification extends BaseNotification
{
    use HasTelegramNotification;
}
