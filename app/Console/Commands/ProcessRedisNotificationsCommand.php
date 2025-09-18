<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificationService;

class ProcessRedisNotificationsCommand extends Command
{
    protected $signature = 'notifications:process-redis';
    protected $description = 'Process notifications from Redis and dispatch jobs to send them';

    public function handle(NotificationService $notificationService)
    {
        $notificationService->processRedisNotifications();
        $this->info('Processed notifications from Redis and dispatched jobs.');
    }
}
