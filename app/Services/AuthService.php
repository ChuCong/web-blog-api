<?php

namespace App\Services;

use App\Core\ServiceResponse;
use App\Models\User;
use App\Repositories\AdminRepository;
use Exception;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    protected $adminRepository;
    // protected $passwordResetRepository;
    public function __construct(
        AdminRepository $adminRepository
        // PasswordResetRepository $passwordResetRepository
    ) 
    {
        $this->adminRepository = $adminRepository;
        // $this->passwordResetRepository = $passwordResetRepository;
    }

    public function login($data) {
        $user = $this->adminRepository->findOne('user_name', $data['user_name']);
        if(empty($user)) {
            throw new Exception('User does not exist');
        }
        if(!Hash::check($data['password'], $user->password)) {
            throw new Exception('Password incorrect');
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        return [
            'data' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer'
        ];
    }

    // public function forgotPassword($email) {
    //     $user = $this->adminRepository->findOne('email', $email);
    //     if(empty($user)) {
    //         throw new Exception('User does not exist');
    //     }
    //     $token = uniqid();
    //     while ($this->passwordResetRepository->findOne('token', $token)) {
    //         $token = uniqid();
    //     }
    //     $data = [
    //         'email' => $email,
    //         'token' => $token,
    //         'created_at' => now(),
    //         'type' => AppConst::TYPE_PASSWORD_RESET_ADMIN
    //     ];
    //     $this->passwordResetRepository->create($data);
    //     $data['name'] = $user->name ?? "";
    //     $data['domain'] = AppConst::DOMAIN_FRONTEND;
    //     dispatch(new SendMail($data));
    //     return true;
    // }

    // public function resetPassword($data) {
    //     $passwordReset = $this->passwordResetRepository->findWhereOne([
    //         ['email', '=', $data['email']],
    //         ['token', '=', $data['token']],
    //         ['type', '=', AppConst::TYPE_PASSWORD_RESET_ADMIN]
    //     ]);
    //     if(!$passwordReset || empty($passwordReset->admin)) {
    //         throwException('Reset password error');
    //     }
    //     $minutes = (strtotime('now') - strtotime($passwordReset->created_at))/60 ;
    //     if($minutes > config('auth.passwords.admins.expire')) {
    //         throwException('Reset password error: token expire');
    //     }
    //     $result = $this->adminRepository->update('id', $passwordReset->user->id, [
    //         'password' => Hash::make($data['password'])
    //     ]);
    //     $this->passwordResetRepository->deleteWhere('email', $data['email']);
    //     return $result;
    // }
}
