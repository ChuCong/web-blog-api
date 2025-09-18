<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseRequest;

class UserChangePassowrdRequest extends BaseRequest
{
   /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'password_old' => 'required|min:6|max:255',
            'password' => 'required|confirmed|min:6|max:255',
            // 'password_confirmation' => 'required|min:6|max:255',
        ];
    }

    public function messages()
    {
        return [
            'password_old' => "Mật khẩu cũ",
            'password' => "Mật khẩu mới",
            'password_confirmation' => "Xác nhận mật khẩu mới",
        ];
    }

}
