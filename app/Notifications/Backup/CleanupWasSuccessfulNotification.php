<?php

namespace App\Notifications\Backup;

use Spatie\Backup\Notifications\Notifications\CleanupWasSuccessfulNotification as BaseNotification;

class CleanupWasSuccessfulNotification extends BaseNotification
{
    use HasTelegramNotification;
}
