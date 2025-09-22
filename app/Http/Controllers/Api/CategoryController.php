<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CategoryService;
use App\Core\CommonUtility;
use Exception;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(
        CategoryService $categoryService,
    )
    {
        $this->categoryService = $categoryService;
    }

    public function getList (Request $request)
    {
        try {
            $limit = $request->input('limit', 10);
            $page = $request->input('page', 1);
            return CommonUtility::getSuccessResponse($this->categoryService->getListCategory($limit, $page), "success");
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return CommonUtility::getErrorResponse("Get list fail");
        }
    }

    public function getBySlug($slug, Request $request)
    {
        try {
            $limit = $request->input('limit', 10);
            $page = $request->input('page', 1);
            return CommonUtility::getSuccessResponse($this->categoryService->getBySlug($slug, $limit, $page), "success");
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return CommonUtility::getErrorResponse("Get detail fail");
        }
    }
}