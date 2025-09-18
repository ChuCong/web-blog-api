<?php

namespace App\Repositories;

use App\Models\Permission;

class PermissionRepository extends BaseRepository
{
    public function getModel()
    {
        return Permission::class;
    }

    public function getListPermissionByType($type){
        return $this->model->select('group_name','name')->where('type',$type)->groupBy('group_name')->get();
    }
}
