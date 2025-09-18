<?php

namespace App\Jobs;

use App\Events\NotificationPushed;
use App\Models\Notification;
use App\Repositories\UserNotificationRepository;
use App\Repositories\UserRepository;
use App\Repositories\CourseManagerRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendNotificationJob implements ShouldQueue
{
    use Queueable;

    protected $notification;
    protected $userRepository;
    protected $userNotificationRepository;
    protected $courseManagerRepository;

    /**
     * Create a new job instance.
     */
    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    /**
     * Execute the job.
     */
    public function handle(UserRepository $userRepository, CourseManagerRepository $courseManagerRepository, 
        UserNotificationRepository $userNotificationRepository): void
    {
        try {
            $userMap = [];
            if ($this->notification->audience === Notification::TYPE_ALL) {
                $userMap = $userRepository->getAllActiveUserIds();
            } elseif ($this->notification->audience === Notification::TYPE_CUSTOM) {
                $userMap = $userRepository->getUserIdsByIds($this->notification->target_ids ?? []);
            } elseif ($this->notification->audience === Notification::TYPE_COURSE) {
                $courseId = $this->notification->target_ids ? $this->notification->target_ids[0] : null;
                if ($courseId) {
                    $userIds = $courseManagerRepository->getUserIdsByCourseId($courseId);
                    $userMap = $userRepository->getUserIdsByIds($userIds);
                }
            }

            $now = Carbon::now();
            $userNotificationData = [];
            foreach ($userMap as $userId => $email) {
                $userNotificationData[] = [
                    'user_id' => $userId,
                    'notification_id' => $this->notification->id,
                    'title' => $this->notification->title,
                    'message' => $this->notification->message,
                    'image' => $this->notification->image,
                    'link' => $this->notification->link,
                    'is_read' => false,
                    'created_at' => $now,
                    'updated_at' => $now
                ];
                event(new NotificationPushed(
                    $email,
                    $this->notification->title,
                    $this->notification->message,
                    $this->notification->type,
                    $this->notification->id
                ));
            }
            if ($userNotificationData) {
                $userNotificationRepository->createMany($userNotificationData);
            }
        } catch (Exception $e) {
            Log::error($e->getTraceAsString());
        }
    }
}
