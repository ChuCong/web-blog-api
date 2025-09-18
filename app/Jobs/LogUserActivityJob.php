<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Repositories\UserLogRepository;
use Illuminate\Bus\Queueable;

class LogUserActivityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;
    protected $courseId;
    protected $lessonId;

    /**
     * Create a new job instance.
     */
    public function __construct($userId, $courseId = null, $lessonId = null)
    {
        $this->userId = $userId;
        $this->courseId = $courseId;
        $this->lessonId = $lessonId;
    }

    /**
     * Execute the job.
     */
    public function handle(UserLogRepository $userLogRepository)
    {
        $userLogRepository->create([
            'user_id' => $this->userId,
            'course_id' => $this->courseId,
            'lesson_id' => $this->lessonId,
        ]);
    }
}
