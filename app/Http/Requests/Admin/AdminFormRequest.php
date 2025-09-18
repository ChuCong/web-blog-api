<?php

namespace App\Http\Requests\Admin;

use Illuminate\Support\Facades\Auth;

use App\Core\AppConst;
use App\Http\Requests\BaseRequest;

class AdminFormRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $validate = [
            'name' => 'required|string|max:255',
            'user_name' => 'required|alpha_dash|max:255|unique:admins,user_name,' . $this->admin . ',id',
            'email' => 'required|email|max:255|unique:admins,email,' . $this->admin . ',id',
            'password' => 'nullable|max:255',
            // 'roles.*' => 'required|exists:roles,name',
            'is_main' => 'required',
            'is_super_admin' => 'required'
        ];
        if($this->isMethod('post')){
            $validate['password'] = 'required|max:255';
        }
        return $validate;
    }

    protected function prepareForValidation()
    {
        $user = Auth::user();
        $dataMerge = [
            'is_main' => !empty($user->is_super_admin) ? 1 : 0,
            'is_super_admin' => !empty($user->is_super_admin) ? (bool)$this->is_super_admin : 0
        ];
        $this->merge($dataMerge);
    }
}