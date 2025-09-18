<?php

namespace App\Http\Controllers\Admin;

use App\Services\PermissionService;
use App\Http\Controllers\Controller;
use App\Core\CommonUtility;
use Exception;
use Illuminate\Support\Facades\Log;

class PermissionController extends Controller
{
    protected $service;

    public function __construct(
        PermissionService $service
    ) {
        $this->service = $service;
    }

    /**
     * Show list category
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return CommonUtility::getSuccessResponse($this->service->getByGroupName(), "success");
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return CommonUtility::getErrorResponse("Get list fail");
        }
    }
}
