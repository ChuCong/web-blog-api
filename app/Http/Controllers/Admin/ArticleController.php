<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ArticleFormRequest;
use App\Services\ArticleService;
use App\Core\CommonUtility;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Services\ImageService;
use App\Http\Requests\ImageRequest;

class ArticleController extends Controller
{
    protected $articleService;
    protected $imageService;

    public function __construct(ArticleService $articleService, ImageService $imageService)
    {
        $this->articleService = $articleService;
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $limit = $request->input('limit', 10);
            $page = $request->input('page', 1);
            $title = $request->input('title', null);
            return CommonUtility::getSuccessResponse($this->articleService->getListArticleAdmin($limit, $page, $title), "success");
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return CommonUtility::getErrorResponse("Get list fail");
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ArticleFormRequest $request)
    {
       try {
            return CommonUtility::getSuccessResponse($this->articleService->create($request->all()), "success");
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return CommonUtility::getErrorResponse("Save article fail");
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            return CommonUtility::getSuccessResponse($this->articleService->find($id), "success");
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return CommonUtility::getErrorResponse("Get detail fail");
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $data = $request->validated();
            return CommonUtility::getSuccessResponse($this->articleService->update($id, $data), "success");
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return CommonUtility::getErrorResponse("Update article fail");
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            return CommonUtility::getSuccessResponse($this->articleService->delete($id), "success");
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return CommonUtility::getErrorResponse("Delete article fail");
        }
    }

    public function uploadImage(ImageRequest $request)
    {
        $paramUpload = "upload";
        $relativePath = "tmp/articles";
        try {
            $url = $this->imageService->uploadFromRequest($request, $paramUpload, $relativePath);
        } catch (Exception $e) {
            return CommonUtility::getErrorResponse("Upload image fail");
        }
        return CommonUtility::getSuccessResponse($url, "success");
    }
}
