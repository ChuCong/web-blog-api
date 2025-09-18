<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\BaseRequest;

class UserForgotPasswordRequest extends BaseRequest
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
            'company_id' => 'required|exists:companies,id,deleted_at,NULL',
         ];
     }
}
