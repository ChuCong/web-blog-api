<?php

namespace App\Http\Middleware;

use App\Core\AppConst;
use App\Models\PersonalAccessKey;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Log;

use const App\Helpers\HTTP_CODE_UNAUTHORIZED;

use function App\Helpers\responseJsonFail;

class Authenticate extends Middleware
{

    /**
     * Determine if the user is logged in to any of the given guards.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $guards
     * @return void
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    protected function authenticate($request, array $guards)
    {
        if (empty($guards)) {
            $guards = [null];
        }

        if(!$request->bearerToken() && ($token = PersonalAccessKey::findKeyAndUpdateLastUsed($request->key, AppConst::GUARD_SANCTUM)) && in_array(AppConst::GUARD_SANCTUM, $guards)){
            $user = $token->user;
            $this->auth->guard(AppConst::GUARD_SANCTUM)->setUser($user);
        }

        if(!$request->bearerToken() && ($token = PersonalAccessKey::findKeyAndUpdateLastUsed($request->key, AppConst::GUARD_SANCTUM)) &&  in_array(AppConst::GUARD_SANCTUM_CLIENT, $guards)){
            $user = $token->user;
            $this->auth->guard(AppConst::GUARD_SANCTUM_CLIENT)->setUser($user);
        }
        
        foreach ($guards as $guard) {
            if ($this->auth->guard($guard)->check()) {
                return $this->auth->shouldUse($guard);
            }
        }

        $this->unauthenticated($request, $guards);
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return responseJsonFail(__("Not authenticated"), HTTP_CODE_UNAUTHORIZED);
        }
    }
}
