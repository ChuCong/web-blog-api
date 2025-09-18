<?php

namespace App\Core;

class ServiceResponse
{
    const RESPONSE_STATUS_SUCCESS = 1;
    const RESPONSE_STATUS_FAIL = 0;

    const HTTP_CODE_SUCCESS = 200;
    const HTTP_CODE_UNAUTHORIZED = 401;
    const HTTP_CODE_UNPROCESSABLE = 422;
    
    protected $status;
    protected $message;
    protected $data;

    public function __construct($status, $message, $data)
    {
        $this->status = $status;
        $this->message = $message;
        $this->data = $data;
    }
    public function getStatus()
    {
        return $this->status;
    }
    public function getMessage()
    {
        return $this->message;
    }
    public function getData()
    {
        return $this->data;
    }
}
