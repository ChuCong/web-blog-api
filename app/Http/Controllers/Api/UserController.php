<?php

namespace App\Http\Controllers\Api;

use App\Core\CommonUtility;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ImageService;
use App\Services\UserService;

use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    protected $userService;
    protected $imageService;

    public function __construct(UserService $userService, ImageService $imageService, )
    {
        $this->userService = $userService;
        $this->imageService = $imageService;
    }

    public function updateAvatar(Request $request)
    {
        try {
            $request->validate([
                'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
            ]);

            $user = Auth::user();
            if (!$user) {
                return CommonUtility::getErrorResponse("Unauthorized", 401);
            }

            $relativePath = "upload/avatars/{$user->id}";
            $avatarUrl = $this->imageService->uploadFromRequest($request, 'avatar', $relativePath);

            if (!$avatarUrl) {
                return CommonUtility::getErrorResponse("Upload failed", 400);
            }

            $this->userService->update([
                'id' => $user->id,
                'avatar' => $avatarUrl,
            ]);

            return CommonUtility::getSuccessResponse(['avatar' => $avatarUrl], "Avatar updated successfully");
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return CommonUtility::getErrorResponse("Update avatar fail");
        }
    }
    public function getProfile(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return CommonUtility::getErrorResponse("Unauthorized", 401);
            }
            $user = $this->userService->find($user->id);
            return CommonUtility::getSuccessResponse($user, "Get user profile success");
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return CommonUtility::getErrorResponse("Get user profile fail");
        }
    }

    public function updateProfile(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return CommonUtility::getErrorResponse("Unauthorized", 401);
            }

            $request->validate([
                'full_name' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:20',
                'sex' => 'nullable',
                'birthday' => 'nullable|date',
            ]);

            $data = $request->only([
                'full_name',
                'phone',
                'sex',
                'birthday'
            ]);
            $data['id'] = $user->id;

            $user = $this->userService->update($data);

            return CommonUtility::getSuccessResponse($user, "Update profile success");
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return CommonUtility::getErrorResponse("Update profile fail");
        }
    }
}
