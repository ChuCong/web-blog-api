<?php

namespace App\Repositories;

use Spatie\Permission\Models\Role;
use App\Repositories\BaseRepository;
use function App\Helpers\paginate;

class RoleRepository extends BaseRepository
{
    public function getModel()
    {
        return Role::class;
    }

    public function getListRoles($limit, $page)
    {
        $result = $this->getQueryBuilder()
            ->orderBy('created_at', 'DESC');
        $total = $result->count();
        $paginate = paginate($total, $limit, $page);
        $offset = $paginate['offset'];
        $data = $result->skip($offset)->take($limit)->get();
        return [
            'data' => $data,
            'paginate' => $paginate
        ];
    }

    public function getById($id)
    {
        $data = Role::query()->with([
            'permissions:name'
        ])->findOrFail($id);

        $data->listPermission = $data->permissions->pluck('name');
        return $data;
    }
    public function getId($id)
    {
        $data = Role::query()->with([
            'permissions:name'
        ])->findOrFail($id);
        return $data;
    }

    public function countSearch(array $params = [])
    {
        $query = Role::query();

        $query->when(!empty($params['name']), function ($q) use ($params) {
            $stringLike = '%' . $params['name'] . '%';
            $q->where('name', 'like', $stringLike);
        });

        // Nếu không phải admin tổng thì loại bỏ role "super_admin"
        $query->when(empty($params['is_super_admin']), function ($q) {
            $q->where('name', '!=', 'super_admin');
        });

        return $query->count();
    }

    public function getListAll()
    {
        return Role::all();
    }

    public function searchRole(array $params = [], $limit = null, $offset = null, $orderBy = 'id', $columns = ['*'])
    {
        $query = Role::query();

        $query->when(!empty($params['name']), function ($q) use ($params) {
            $stringLike = '%' . $params['name'] . '%';
            $q->where('name', 'like', $stringLike);
        });

        $query->when(empty($params['is_super_admin']), function ($q) {
            $q->where('name', '!=', 'super_admin');
        });

        if ($limit) {
            $query->limit($limit);
        }
        if ($offset) {
            $query->offset($offset);
        }

        return $query->orderByDesc($orderBy)->get($columns);
    }
}
