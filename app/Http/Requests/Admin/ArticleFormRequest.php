<?php

namespace App\Http\Requests\Admin;

use App\Core\CommonUtility;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
class ArticleFormRequest extends FormRequest
{
     /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = [
            'title' => 'required|string',
            'description' => 'string|nullable',
            'content' => 'required|string',
            'category_id' => 'required|integer',
            'url' => 'string|nullable',
            'active' => 'boolean',
            'seo_key' => 'string|nullable',
            'seo_title' => 'string|nullable',
            'seo_description' => 'string|nullable',
        ];
        return $rules;
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(CommonUtility::getErrorResponseErrorCode(422, $validator->errors()->first()));
    }
}
