<?php

namespace App\Notifications\Backup;

use Spatie\Backup\Notifications\Notifications\CleanupHasFailedNotification as BaseNotification;

class CleanupHasFailedNotification extends BaseNotification
{
    use HasTelegramNotification;
}
