<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;

class RoleFormRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|min:1|unique:roles,name,'.$this->role.',id',
            'permissions.*' => 'required|exists:permissions,name',
        ];
    }

    public function attributes()
    {
        return [
            'permissions.*' => 'Tên quyền'
        ];
    }

    public function messages()
    {
        return [
            
        ];
    }
   
}
