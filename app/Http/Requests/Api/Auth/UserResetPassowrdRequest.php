<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\BaseRequest;

class UserResetPassowrdRequest extends BaseRequest
{
    /**
      * Get the validation rules that apply to the request.
      *
      * @return array
      */
     public function rules()
     {
         return [
            'email' => 'required|exists:admins,email',
            'token' => 'required',
            'password' => 'required|confirmed|min:6|max:255',
            'company_id' => 'required|exists:companies,id,deleted_at,NULL',
         ];
     }
 
     public function messages()
     {
         return [
             
         ];
     }
}
