<?php

namespace App\Jobs;

use App\Services\NotificationService;
use App\Services\RedisService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ScheduleNotificationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $notificationId;
    /**
     * Create a new job instance.
     */
    public function __construct($notificationId = null)
    {
        $this->notificationId = $notificationId;
    }

    /**
     * Execute the job.
     */
    /**
     * @param NotificationService $notificationService
     * @param int|null $notificationId
     */
    public function handle(NotificationService $notificationService, RedisService $redisService)
    {
        try {
            if ($this->notificationId) {
                $pattern = "notification:{$this->notificationId}:*";
                $redisService->delRedisByPattern($pattern);
            }
            $notificationService->scheduleNotifications();
        } catch (Exception $e) {
            Log::error('ScheduleNotificationsJob error: ' . $e->getMessage());
        }
    }
}
