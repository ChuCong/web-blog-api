<?php

namespace App\Repositories;

use App\Models\Media;
use App\Repositories\BaseRepository;

class MediaRepository extends BaseRepository
{

    public function getModel()
    {
        return Media::class;
    }
}
