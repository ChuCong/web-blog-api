<?php

namespace App\Services;

use App\Core\AppConst;
use App\Repositories\PermissionRepository;

class PermissionService
{
    protected $repository;

    public function __construct(
        PermissionRepository $repository)
    {
        $this->repository = $repository;
    }
    
    public function getByGroupName()
    {
        $permissions = $this->repository->getAll();
        return $permissions->groupBy('group_name');
    }
}
