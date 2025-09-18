<?php

namespace App\Services;

use App\Core\AppConst;
use App\Repositories\AdminRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use function App\Helpers\paginate;

class AdminService
{
    protected $repository;
    protected $companyRepository;

    public function __construct(
        AdminRepository $repository,
    ) {
        $this->repository = $repository;
    }

    public function getAll()
    {
        return $this->repository->getAll();
    }

    public function getListAdmin($limit, $page, $filters = [])
    {
        $limit = $limit ?? 10;
        $page = $page ?? 1;
        return $this->repository->getListAdmin($limit, $page, $filters);
    }

    public function create($data)
    {
        if (!empty($data['data']['password'])) {
            $data['data']['password'] = Hash::make($data['data']['password']);
        }
        $user = $this->repository->create($data['data']);
        $user->assignRole($data['roles']);
        return $user;
    }

    public function getDetail($id)
    {
        $user = $this->repository->getById($id);
        $user->listPermission = $user->getAllPermissions()->pluck('name');
        return $user;
    }

    public function update($data)
    {
        $user = $this->repository->getById($data['id']);
        $user->syncRoles($data['roles']);
        if (empty($data['data']['password'])) {
            unset($data['data']['password']);
        } else {
            $data['data']['password'] = Hash::make($data['data']['password']);
        }
        return (bool)$this->repository->update($data['id'], $data['data']);
    }

    public function delete($id)
    {
        $user = $this->repository->getById($id);
        $user->roles()->detach();
        $user->syncRoles([]);
        return (bool)$this->repository->delete($id);
    }

    public function changePassword($data)
{
    $user = $this->repository->getById($data['id']);
    $user->password = Hash::make($data['new_password']);
    
    // Tạo mảng chứa các thuộc tính cần cập nhật
    $attributes = [
        'password' => $user->password,
        // Thêm các thuộc tính khác nếu cần
    ];

    return $this->repository->update($user->id, $attributes);
}
}
