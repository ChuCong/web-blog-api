<?php

namespace App\Jobs;

use App\Models\Search;
use App\Repositories\SearchRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncSearchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $type;
    protected $sourceId;
    protected $action; // create|update|delete
    protected $title;
    protected $data;

    const ACTION_CREATE = 'create';
    const ACTION_UPDATE = 'update';
    const ACTION_DELETE = 'delete';

    /**
     * @param string $type (Search::COURSE_TYPE, ...)
     * @param int $sourceId
     * @param string $action ('create', 'update', 'delete')
     * @param string $title
     * @param array $data (optional)
     */
    public function __construct($type, $sourceId, $action, $title=null, $data = [])
    {
        $this->type = $type;
        $this->sourceId = $sourceId;
        $this->action = $action;
        $this->title = $title;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->action === self::ACTION_DELETE) {
            Search::where('type', $this->type)
                ->where('source_id', $this->sourceId)
                ->delete();
            return;
        }

        // create or update
        Search::updateOrCreate(
            [
                'type' => $this->type,
                'source_id' => $this->sourceId,
            ],
            [
                'title' => $this->title,
                'data' => $this->data,
            ]
        );
    }
}
