<?php

namespace App\Http\Controllers\Api;

use App\Core\CommonUtility;
use App\Events\SendMailEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\UserForgotPasswordRequest;
use App\Http\Requests\Api\Auth\UserLoginRequest;
use App\Http\Requests\Api\Auth\UserResetPassowrdRequest;
use App\Http\Requests\Api\Auth\UserRegisterRequest;
use App\Http\Requests\Auth\UserChangePassowrdRequest;
use App\Models\Information;
use App\Models\User;
use App\Services\AuthUserService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    protected AuthUserService $authService;

    public function __construct(
        AuthUserService $authService
    )
    {
        $this->authService = $authService;
    }

    /**
     * Display a listing by params of the resource.
     *
     * @param  \App\Http\Requests\Auth\UserLoginRequest
     * @return \Illuminate\Http\Response
     */
    public function login(UserLoginRequest $request)
    {
//         return $this->baseAction(function() use($request){
//             $data = [
//                 "password" => $request->password,
//                 "company_id" => $request->company_id,
//                 'email' => $request->email,
//                 "conditions" => [
//                     ['email', '=', $request->email],
// //                    ['company_id', '=', $request->company_id]
//                 ]
//             ];
//             return $this->authService->login($data);
//         }, "Login success", "Login error");
    }
    public function register(UserRegisterRequest $request)
    {
        // return $this->baseAction(function() use($request) {
        //     $uuid = (string) Str::uuid();
        //     $data = [
        //         "password" => $request->password,
        //         "name" => $request->name,
        //         "company_id" => $request->company_id,
        //         "email" => $request->email,
        //         "key" => $request->key,
        //         "uuid" => $uuid,
        //     ];
        //     return $this->authService->register($data);
        // }, "Register success", "Register error");
    }
    public function verifyEmail($userId)
    {
//         $user = $this->authService->getUser($userId);
//         if ($user) {
//             $user->update([
//                 'status' => User::ACTIVE
//             ]);
//             $infor = new Information();
//             $infor->email = $user->email;
//             $content = 'Đăng kí tài khoản thành công';
//             $infor->data = [
//                 'content' => $content,
//             ];
//             $infor->mailContentHtml = 'emails.send-email';
//             event(new SendMailEvent($infor));
//             header('Location: ' .config('app.front_end_url'));
// //            header('Location: http://localhost:8888?check_verify=true');
//             exit();

//         }

//         return response()->json([
//             'status' => 0,
//             'message' => 'Verify email failed!'
//         ]);
    }

//    public function resendEmail(Request $request)
//    {
//        $userId = $request->user_id;
//        if (isset($userId)) {
//            $user = $this->authService->getUser($userId);
//            if ($user) {
//                $user->token = Str::random(40);
//                if ($this->sendMail($user)) {
//                    return response()->json([
//                        'status' => 1,
//                        'message' => 'Resend email successfully!'
//                    ]);
//                }
//            }
//        }
//
//        return response()->json([
//            'status' => 1,
//            'message' => 'Resend email fail!'
//        ]);
//    }

    public function forgotPassword(UserForgotPasswordRequest $request)
    {
        // return $this->baseActionTransaction(function() use($request){
        //     $data = [
        //         "create" => [
        //             'created_at' => now(),
        //             'email' => $request->email,
        //             'company_id' => $request->company_id,
        //             'type' => AppConst::TYPE_PASSWORD_RESET_USER,
        //             'domain' => AppConst::DOMAIN_FRONTEND
        //         ],
        //         "conditions" => [
        //             ['email', '=', $request->email],
        //             ['company_id', '=', $request->company_id]
        //         ]
        //     ];
        //     return $this->authService->forgotPassword($data);
        // }, "Forgot password success", "Forgot password error");
    }

    // public function resetPassword(UserResetPassowrdRequest $request)
    // {
    //     return $this->baseActionTransaction(function() use($request){
    //         $data = [
    //             "email" => $request->email,
    //             "password" => $request->password,
    //             "conditions" => [
    //                 ['email', '=', $request->email],
    //                 ['token', '=', $request->token],
    //                 ['company_id', '=', $request->company_id],
    //                 ['type', '=', AppConst::TYPE_PASSWORD_RESET_USER]
    //             ]
    //         ];
    //         return $this->authService->resetPassword($data);
    //     }, "Reset password success", "Reset password error");
    // }

    // public function changePassword(UserChangePassowrdRequest $request)
    // {
    //     $user = $request->user();
    //     $data = [
    //         'password' => $request->password,
    //         'password_old' => $request->password_old,
    //     ];
    //     return $this->authService->changePassword($user, $data);
    // }

    // public function me(Request $request)
    // {
    //     return $this->baseAction(function() use($request){
    //         return $request->user();
    //     }, "Get user success", "Get user error");
    // }

    public function loginGoogle(Request $request) {
        try {
            return CommonUtility::getSuccessResponse($this->authService->loginByGoogle($request->all()), "Login success");
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return CommonUtility::getErrorResponse("Login error");
        }
    }
}
