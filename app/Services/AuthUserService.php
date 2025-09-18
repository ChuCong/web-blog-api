<?php

namespace App\Services;
use App\Core\CommonUtility;
use App\Core\ServiceResponse;
use App\Events\SendMailEvent;
use App\Models\Information;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// use App\Jobs\Auth\SendMail;
use App\Models\PersonalAccessKey;
use App\Models\User;
use App\Repositories\PasswordResetRepository;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Google\Client as Google_Client;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;

class AuthUserService
{
    protected $userRepository;
    protected $passwordResetRepository;
    public function __construct(
        UserRepository $userRepository,
        PasswordResetRepository $passwordResetRepository
    ) {
        $this->userRepository = $userRepository;
        $this->passwordResetRepository = $passwordResetRepository;
    }

    public function login($data)
    {
        $user = $this->userRepository->findWhereOne($data['conditions']);
        if (empty($user)) {
            CommonUtility::throwException('User does not exist');
        }

        if (!Hash::check($data['password'], $user->password)) {
            CommonUtility::throwException('Password incorrect');
        }

        if ($user->status == 0) {
            CommonUtility::throwException('Tài khoản của bạn chưa được xác thực! Vui lòng kiểm tra lại email để xác thực tài khoản.');
        }
        if (!$user->active) {
            CommonUtility::throwException('Tài khoản của bạn bị khóa ! Vui lòng liên hệ với admin để mở khóa.');
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        return [
            'status' => CommonUtility::RESPONSE_STATUS_SUCCESS,
            'data' => $user,
            'company_id' => $data['company_id'],
            'access_token' => $token,
            'token_type' => 'Bearer'
        ];
    }

    public function register($data)
    {

        try {
            DB::beginTransaction();
            $data['password'] = Hash::make($data['password']);
            $user = $this->userRepository->create($data);
            $user->token = Str::random(40);
            // $this->sendMail($user);

            DB::commit();
            return [
                'status' => CommonUtility::RESPONSE_STATUS_SUCCESS,
                'data' => $user,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getTraceAsString());
        }
    }

    // public function sendMail($user)
    // {
    //     $infor = new Information();
    //     $infor->email = $user->email;
    //     $infor->data = [
    //         "user" => $user
    //     ];
    //     $infor->mailContentHtml = 'emails.reminder';
    //     event(new SendMailEvent($infor));
    //     //        Mail::send('emails.reminder', ['user' => $user], function ($m) use ($user) {
    //     //            $m->to($user->email, $user)
    //     //                ->subject('Hanquocnori - Xác thực người dùng');
    //     //            $m->from(env('MAIL_FROM_ADDRESS'), 'Hanquocnori - Xác thực người dùng');
    //     //        });
    // }

    public function getUser($user_id)
    {
        return $this->userRepository->getById($user_id);
    }

    public function forgotPassword($data)
    {
        $user = $this->userRepository->findWhereOne($data['conditions']);
        if (empty($user)) {
            CommonUtility::throwException('User does not exist');
        }
        $token = uniqid();
        while ($this->passwordResetRepository->findOne('token', $token)) {
            $token = uniqid();
        }
        $data['create']['token'] = $token;
        $data['create']['name'] = $user->name ?? "";
        $this->passwordResetRepository->create($data['create']);
        // dispatch(new SendMail($data['create']));
        return true;
    }

    public function resetPassword($data)
    {
        $passwordReset = $this->passwordResetRepository->findWhereOne($data['conditions']);
        if (!$passwordReset || empty($passwordReset->user)) {
            CommonUtility::throwException('Reset password error');
        }
        $minutes = (strtotime('now') - strtotime($passwordReset->created_at)) / 60;
        if ($minutes > config('auth.passwords.admins.expire')) {
            CommonUtility::throwException('Reset password error: token expire');
        }
        $result = $this->userRepository->update('id', $passwordReset->user->id, [
            'password' => Hash::make($data['password'])
        ]);
        $this->passwordResetRepository->deleteWhere('email', $data['email']);
        return $result;
    }

    public function changePassword($user, $data)
    {
        if (empty($user)) {
            CommonUtility::throwException('User does not exist');
        }

        if (!Hash::check($data['password_old'], $user->password)) {
            CommonUtility::throwException('Password incorrect');
        }
        $result = $this->userRepository->update('id', $user->id, [
            'password' => Hash::make($data['password'])
        ]);
        $user->tokens()->delete();
        return $result;
    }

    public function loginByGoogle($data)
    {
        $client = new Google_Client();
        $client->setClientId(config('google.client_id'));
        $client->setClientSecret(config('google.client_secret'));
        $client->setRedirectUri(config('google.redirect_uri'));

        $client->setHttpClient(new Client(['verify' => false]));
        $googleResult = $client->verifyIdToken($data['token']); 

        $user = User::where('email', $googleResult['email'])->first();
        if (!$user) {
            $user = User::create([
                'full_name' => $googleResult['name'],
                'email' => $googleResult['email'],
                'active' => User::ACTIVE,
                'avatar' => isset($googleResult['picture']) ? $googleResult['picture'] : null,
                'password' => Hash::make(rand(100000, 999999)),
                'sex' => 1,
                'type' => 0
            ]);
        }
        if ($user->active == User::DELETED) {
            return [
                'status' => CommonUtility::RESPONSE_STATUS_FAIL,
                'message' => "Tài khoản không hoạt động"
            ];
        }
        Auth::login($user);
        $token = $user->createToken('auth_token')->plainTextToken;
        return [
            'status' => CommonUtility::RESPONSE_STATUS_SUCCESS,
            'data' => $user,
            'company_id' => $user['company_id'],
            'access_token' => $token,
            'token_type' => 'Bearer'
        ];
    }
}
