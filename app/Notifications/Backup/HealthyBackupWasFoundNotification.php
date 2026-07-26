<?php

namespace App\Notifications\Backup;

use Spatie\Backup\Notifications\Notifications\HealthyBackupWasFoundNotification as BaseNotification;

class HealthyBackupWasFoundNotification extends BaseNotification
{
    use HasTelegramNotification;
}
