<?php

namespace App\Repositories;

use App\Models\Admin;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Log;

use function App\Helpers\paginate;

class AdminRepository extends BaseRepository
{

    public function getModel()
    {
        return Admin::class;
    }
    public function getById($id)
    {
        $data = $this->getQueryBuilder()
            ->with(['roles:id,name'])
            ->find($id);
        return $data;
    }

    public function getListAdmin($limit, $page, $filters = [])
    {
        $query = $this->model->where('is_super_admin', '!=', 1)->orderBy('created_at', 'desc');
        if (!empty($filters['name'])) {
            $search = $filters['name'];
            $query->where(function ($sub) use ($search) {
                $sub->where('user_name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }
        if (!empty($filters['user_name'])) {

            $query->where('user_name', 'like', '%' . $filters['user_name'] . '%');
        }
        if (!empty($filters['email'])) {
            $query->where('email', 'like', '%' . $filters['email'] . '%');
        }
        $total = $query->count();
        $offset = ($page - 1) * $limit;
        $data = $query->skip($offset)->take($limit)->get();
        $paginate = paginate($total, $limit, $page);
        return [
            'data' => $data,
            'paginate' => $paginate
        ];
    }
}
