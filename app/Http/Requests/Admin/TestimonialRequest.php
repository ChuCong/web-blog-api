<?php

namespace App\Http\Requests\Admin;

use App\Core\CommonUtility;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
class TestimonialRequest extends FormRequest
{
     /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
            'url_course' => 'string|nullable',
            'lang' => 'required|string', 
            'introduction'=> 'required|string',
            'media_id' =>'integer|nullable'
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(CommonUtility::getErrorResponseErrorCode(422, $validator->errors()->first()));
    }
}
