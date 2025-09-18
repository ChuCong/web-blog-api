<?php

namespace App\Core;

use Exception;
use Illuminate\Support\Facades\Response;

class CommonUtility
{
    const RESPONSE_STATUS_SUCCESS = 1;
    const RESPONSE_STATUS_FAIL = 0;

    const HTTP_CODE_SUCCESS = 200;
    const HTTP_CODE_UNAUTHORIZED = 401;
    const HTTP_CODE_UNPROCESSABLE = 422;

    public static function getErrorResponse($message)
    {
        return Response::json(["status" => 0, "data" => null, "message" => $message]);
    }

    public static function getErrorResponseErrorCode($errorCode, $message)
    {
        return Response::json(["status" => 0, "errorCode" => $errorCode, "data" => null, "message" => $message]);
    }

    public static function getSuccessResponse($data, $message)
    {
        return Response::json(["status" => 1, "data" => $data, "message" => $message]);
    }

    public static function getSuccessResponseWithMoreData($data, $message, $moreData)
    {
        return Response::json(["status" => 1, "data" => $data, "message" => $message, "other" => $moreData]);
    }

    public static function getResponse($data, $message)
    {
        return ['data' => $data, 'message' => $message];
    }

    /**
     * Random number of character
     * @param $num
     */
    public static function randomCharacter($num)
    {
        $seed = str_split(
            'abcdefghijklmnopqrstuvwxyz'
                . 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' . '0123456789'
        ); // and any other characters
        shuffle($seed); // probably optional since array_is randomized; this may be redundant
        $rand = '';
        foreach (array_rand($seed, $num) as $k) {
            $rand .= $seed[$k];
        }

        return $rand;
    }

    public static function getFileName($fileUrl)
    {
        $urlArr = explode("/", $fileUrl);
        $arrLength = count($urlArr);
        // get last element --> file name
        return $urlArr[$arrLength - 1];
    }

    public static function convertScoreToCertificate($score, $level)
    {
        if ($level == 1 && $score >= 80) {
            return 1;
        }
        if ($level == 1 && $score >= 80) {
            return 2;
        }
        if ($level == 2 && $score >= 120) {
            return 3;
        }
        if ($level == 2 && $score >= 150) {
            return 4;
        }
        if ($level == 2 && $score >= 190) {
            return 5;
        }
        if ($level == 2 && $score >= 230) {
            return 6;
        }
        return 0;
    }
    public static function getUser($fileUrl)
    {
        $urlArr = explode("/", $fileUrl);
        $arrLength = count($urlArr);
        // get last element --> file name
        return $urlArr[$arrLength - 1];
    }

    public static function throwException(string|null $message, int $code = AppConst::CODE_EXCEPTION_MESSAGE): Exception
    {
        throw new Exception(__($message), $code);
    }

    public static function responseJsonSuccess($data = [], $message = '')
    {
        return response(
            [
                'status' => self::RESPONSE_STATUS_SUCCESS,
                'message' => $message,
                'data' => $data
            ],
            self::HTTP_CODE_SUCCESS
        );
    }

    public static function responseJsonFail($message = '', $httpCode = self::HTTP_CODE_SUCCESS, $errors = [])
    {
        return response(
            [
                'status' => self::RESPONSE_STATUS_FAIL,
                'message' => $message == false ? __("Error") : $message,
            ],
            $httpCode
        );
    }

    public static function responseJsonFailMultipleErrors($errors = [], $message = '', $httpCode = self::HTTP_CODE_UNPROCESSABLE)
    {
        return response(
            [
                'status' => self::RESPONSE_STATUS_FAIL,
                'message' => $message,
                'errors' => $errors,
            ],
            $httpCode
        );
    }
}
