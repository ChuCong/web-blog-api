<?php

namespace App\Http\Requests\Api\Auth;

use App\Models\PersonalAccessKey;
use Illuminate\Foundation\Http\FormRequest;

class UserRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $this->prepareForValidation();
        return [
            'email' => 'required|email|max:255|unique:users,email,' . $this->email . ',id',
            'password' => 'required',
            'name' => 'required',
            'company_id' => 'required|exists:companies,id,deleted_at,NULL',
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
