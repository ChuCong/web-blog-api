<?php

namespace App\Http\Controllers;

use App\Core\AppConst;
use Closure;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

use function App\Helpers\responseJsonFail;
use function App\Helpers\responseJsonSuccess;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Display a listing of the resource.
     *
     * @param  \Closure  $closure
     * @param  string|null $messageSuccess
     * @param  string|null $messageError
     * @param  string|null  ...$params
     * @return \App\Helpers\responseJsonSuccess|\App\Helpers\responseJsonFail
     */
    protected function baseAction(Closure $closure, $messageSuccess = 'Success', $messageError = 'Error', ...$params)
    {
        try {
            $result = $closure();
        } catch (Throwable $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            return responseJsonFail(AppConst::CODE_EXCEPTION_MESSAGE == $e->getCode() ? $e->getMessage() :  __($messageError));
        }

        return ($result || is_array($result)) ? responseJsonSuccess($result, __($messageSuccess)) : responseJsonFail(__($messageError));
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Closure  $closure
     * @param  string|null $messageSuccess
     * @param  string|null $messageError
     * @param  string|null  ...$params
     * @return \App\Helpers\responseJsonSuccess|\App\Helpers\responseJsonFail
     */
    protected function baseActionTransaction(Closure $closure, $messageSuccess = 'Success', $messageError = 'Error', ...$params)
    {
        DB::beginTransaction();
        try {
            $result = $closure();
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            Log::warning($e->getMessage());
            Log::warning($e->getTraceAsString());
            return responseJsonFail(AppConst::CODE_EXCEPTION_MESSAGE == $e->getCode() ? $e->getMessage() :  __($messageError));
        }

        return $result ? responseJsonSuccess($result, __($messageSuccess)) : responseJsonFail(__($messageError));
    }
}
