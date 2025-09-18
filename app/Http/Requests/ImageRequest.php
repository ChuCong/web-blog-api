<?php

namespace App\Http\Requests;

use App\Core\CommonUtility;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ImageRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'upload' => [
                'required',
                'mimetypes:image/jpeg,image/png,image/gif,image/bmp,image/webp',
                'max:10000000',
            ],
        ];
    }


    public function messages()
    {
        return [
            'upload.required'  => trans('api.image.required'),
            'upload.mimetypes'  => trans('api.image.format'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(CommonUtility::getErrorResponseErrorCode(422, $validator->errors()->first()));
    }
}
