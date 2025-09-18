<?php

namespace App\Helpers;

const RESPONSE_STATUS_SUCCESS = 1;
const RESPONSE_STATUS_FAIL = 0;

const HTTP_CODE_SUCCESS = 200;
const HTTP_CODE_UNAUTHORIZED = 401;
const HTTP_CODE_UNPROCESSABLE = 422;

function responseJsonSuccess($data = [], $message = '')
{
    return response(
        [
            'status' => RESPONSE_STATUS_SUCCESS,
            'message' => $message,
            'data' => $data
        ]
    , HTTP_CODE_SUCCESS);
}

function responseJsonFail($message = '', $httpCode = HTTP_CODE_SUCCESS, $errors=[])
{
    return response(
        [
            'status' => RESPONSE_STATUS_FAIL,
            'message' => $message == false ? __("Error") : $message,
        ]
    , $httpCode);
}

function responseJsonFailMultipleErrors($errors=[], $message = '', $httpCode = HTTP_CODE_UNPROCESSABLE)
{
    return response(
        [
            'status' => RESPONSE_STATUS_FAIL,
            'message' => $message,
            'errors' => $errors,
        ]
    , $httpCode);
}
