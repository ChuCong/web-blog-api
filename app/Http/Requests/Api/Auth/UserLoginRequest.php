<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\BaseRequest;
use App\Models\PersonalAccessKey;

class UserLoginRequest extends BaseRequest
{
   /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $this->prepareForValidation();
        return [
            'email' => 'required|email|exists:users,email',
            'password' => 'required'
        ];
    }

    public function messages()
    {
        return [

        ];
    }

    protected function prepareForValidation()
    {
        $company_id = PersonalAccessKey::findCompanyIdByKey($this->key);

        $this->merge([
            'company_id'=> $company_id
        ]);
    }

}
