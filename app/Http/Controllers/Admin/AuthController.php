<?php

namespace App\Http\Controllers\Admin;

use App\Core\CommonUtility;
use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Exception;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(Request $request)
    {
        $request->validate([
            'user_name' => 'required',
            'password' => 'required'
        ]);

        try {
            return CommonUtility::getSuccessResponse($this->authService->login($request->all()), "success");
        } catch (Exception $e) {
            return CommonUtility::getErrorResponse("Delete category fail: " . $e->getMessage());
        }
    }
}
