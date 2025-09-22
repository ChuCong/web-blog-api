<?php

namespace App\Services;

use App\Repositories\MediaRepository;

class MediaService
{
    protected $mediaRepository;
    public function __construct(MediaRepository $mediaRepository)
    {
        $this->mediaRepository = $mediaRepository;
    }
    public function getAll()
    {
        return $this->mediaRepository->getAll();
    }
    public function create(array $data)
    {
        return $this->mediaRepository->create($data);
    }
    public function find($id)
    {
        return $this->mediaRepository->getById($id);
    }
    public function update($id, array $data)
    {
        return $this->mediaRepository->update($id, $data);
    }
    public function delete($id)
    {
        return $this->mediaRepository->delete($id);
    }

    public function save($url, $type)
    {
        $data = [
            'alt' => "image",
            'src' => $url,
            'name' => "image",
            'type' => $type
        ];
        return $this->create($data);
    }

    public function updateOrCreate($media)
    {
        if (!isset($media['id'])) {
            $media['id'] = null;
        }
        return $this->mediaRepository->updateOrCreate($media['id'], $media);
    }
}
