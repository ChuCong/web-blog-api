<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Resource;
use App\Models\News;
use App\Models\Search;
use App\Jobs\SyncSearchJob;

class SyncAllToSearch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'search:sync-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all Course, Lesson, Resource, News to search table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Syncing Courses...');
        Course::where('active', 1)->chunk(100, function ($courses) {
            foreach ($courses as $course) {
                dispatch_sync(new SyncSearchJob(
                    Search::COURSE_TYPE,
                    $course->id,
                    SyncSearchJob::ACTION_CREATE,
                    $course->title,
                    ['slug' => $course->slug]
                ));
            }
        });

        $this->info('Syncing Lessons...');
        Lesson::whereNotNull('reference_id')->chunk(100, function ($lessons) {
            foreach ($lessons as $lesson) {
                // Lấy slug course nếu cần
                $courseSlug = optional($lesson->course)->slug;
                dispatch_sync(new SyncSearchJob(
                    Search::LESSON_TYPE,
                    $lesson->id,
                    SyncSearchJob::ACTION_CREATE,
                    $lesson->title,
                    ['slug' => $lesson->slug, 'course_slug' => $courseSlug]
                ));
            }
        });

        $this->info('Syncing Resources...');
        Resource::chunk(100, function ($resources) {
            foreach ($resources as $resource) {
                // Lấy link ưu tiên media nếu có, không thì lấy url
                $link = $resource->url;
                if ($resource->media_id && $resource->media) {
                    $link = $resource->media->src_url;
                }
                dispatch_sync(new SyncSearchJob(
                    Search::RESOURCE_TYPE,
                    $resource->id,
                    SyncSearchJob::ACTION_CREATE,
                    $resource->title,
                    ['link' => $link]
                ));
            }
        });

        $this->info('Syncing News...');
        News::chunk(100, function ($newsList) {
            foreach ($newsList as $news) {
                dispatch_sync(new SyncSearchJob(
                    Search::NEWS_TYPE,
                    $news->id,
                    SyncSearchJob::ACTION_CREATE,
                    $news->title,
                    ['link' => $news->link]
                ));
            }
        });

        $this->info('Sync completed!');
        return 0;
    }
}
