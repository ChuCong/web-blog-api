<?php

namespace App\Http\Controllers\Admin;

use App\Core\CommonUtility;
use App\Http\Controllers\Controller;
use App\Http\Requests\ImageRequest;
use App\Services\ImageService;
use Exception;

class ImageController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function uploadImage(ImageRequest $request)
    {
        $paramUpload = "upload";
        $relativePath = "tmp/images";
        try {
            $url = $this->imageService->uploadFromRequest($request, $paramUpload, $relativePath);
        } catch (Exception $e) {
            return CommonUtility::getErrorResponse("Upload image fail");
        }
        return CommonUtility::getSuccessResponse($url, "success");
    }
}
