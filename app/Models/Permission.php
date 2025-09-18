<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as PermissionModel;

class Permission extends PermissionModel
{
    protected $appends = ['name_translate'];
    public function getNameTranslateAttribute($value){
        return __($this->name);
    }
}
