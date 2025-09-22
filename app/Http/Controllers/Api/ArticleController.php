<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ArticleService;
use App\Core\CommonUtility;
use Exception;
use Illuminate\Support\Facades\Log;

class ArticleController extends Controller
{
    protected $articleService;

    public function __construct(
        ArticleService $articleService,
    )
    {
        $this->articleService = $articleService;
    }

    public function getList (Request $request)
    {
        try {
            $limit = $request->input('limit', 10);
            $page = $request->input('page', 1);
            return CommonUtility::getSuccessResponse($this->articleService->getListArticleApi($limit, $page), "success");
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return CommonUtility::getErrorResponse("Get list fail");
        }
    }

    public function getBySlug($slug)
    {
        try {
            return CommonUtility::getSuccessResponse($this->articleService->getBySlug($slug), "success");
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return CommonUtility::getErrorResponse("Get detail fail");
        }
    }
}