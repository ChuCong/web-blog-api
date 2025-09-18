<?php

namespace App\Services;

use App\Core\AppConst;
use App\Repositories\RoleRepository;
use function App\Helpers\paginate;
use Illuminate\Support\Facades\Log;

class RoleService
{
    protected $repository;

    public function __construct(
        RoleRepository $repository
    )
    {
        $this->repository = $repository;
    }
    
    public function getAll()
    {
        return $this->repository->getAll();
    }

    public function getAllPaginate($limit, $page) {
        $limit = $limit ?? 10;
        $page = $page ?? 1;
        return $this->repository->getListRoles($limit, $page);
    }

    public function getById($id)
    {
        return $this->repository->getById($id);
    }

    public function create($data)
    {
        $role = $this->repository->create([
            'name' => $data['name'],
        ]);
        $role->syncPermissions($data['permissions'] ?? []);
        return $role;
    }

    public function update($data, $id)
    {
        $role = $this->repository->getId($id);
        if($role->name == AppConst::SUPER_ADMIN_ROLE_NAME){
            return false;
        }
        Log::info($data['name']);
        $role->syncPermissions($data['permissions'] ?? []);
        return $role->update($data);
    }

    public function delete($id)
    {
        $role = $this->repository->getById($id);
        if($role->name == AppConst::SUPER_ADMIN_ROLE_NAME){
            return false;
        }
        $role->syncPermissions([]);
        return (bool)$this->repository->delete($id);
    }

}
