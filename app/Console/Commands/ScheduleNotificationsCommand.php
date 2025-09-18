<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificationService;

class ScheduleNotificationsCommand extends Command
{
    protected $signature = 'notifications:schedule';
    protected $description = 'Schedule notifications for the next hour and push to Redis';

    public function handle(NotificationService $notificationService)
    {
        $notificationService->scheduleNotifications();
        $this->info('Scheduled notifications pushed to Redis.');
    }
}
