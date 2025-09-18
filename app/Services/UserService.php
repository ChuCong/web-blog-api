<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Log;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getListUser($limit, $page, $filters = [], $sortField, $sortOrder)
    {
        $limit = $limit ?? 10;
        $page = $page ?? 1;
        return $this->userRepository->getListUser($limit, $page, $filters, $sortField, $sortOrder);
    }

    public function create($data)
    {
        return $this->userRepository->create($data);
    }

    public function update($data)
    {
        return (bool)$this->userRepository->update($data['id'], $data);
    }

    public function delete($id)
    {
        return (bool)$this->userRepository->update($id, ['status' => User::DELETED]);
    }

    public function find($id)
    {
        return $this->userRepository->findOne('id', $id);
    }

    public function updatetatus($id, $status)
    {
        return (bool)$this->userRepository->update($id, ['active' => $status]);
    }

    public function countTotalUsers($startDate = null, $endDate = null)
    {
        return $this->userRepository->countTotalUsers($startDate, $endDate);
    }

    public function countActiveAndCompletedUsers($startDate = null, $endDate = null)
    {
        return $this->userRepository->countActiveAndCompletedUsers($startDate, $endDate);
    }

    public function topStudents($limit = 5, $startDate = null, $endDate = null)
    {
        return $this->userRepository->topStudents($limit, $startDate, $endDate);
    }
}