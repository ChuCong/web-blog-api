<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Core\CommonUtility;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Core\AppConst;
use App\Services\AdminService;
use App\Http\Requests\Admin\AdminFormRequest;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    protected $service;

    public function __construct(AdminService $service)
    {
        $this->service = $service;
        $this->middleware('permission:create_user')->only('store');
        $this->middleware('permission:update_user')->only('update');
        $this->middleware('permission:delete_user')->only('destroy');
        $this->middleware('permission:view_user')->only('show');
        $this->middleware('permission:list_user')->only(['index', 'search']);
    }

    /**
     * Display a listing of the resource.
     *
     */
    public function index(Request $request)
    {
        try {
            $limit = $request->input('limit', 10);
            $page = $request->input('page', 1);
            $name = $request->input('name');
            $user_name = $request->input('user_name');
            $email = $request->input('email');
            $filters = [
                'name' => $name,
                'user_name' => $user_name,
                'email' => $email
            ];
            $result = $this->service->getListAdmin($limit, $page, $filters);
            return CommonUtility::getSuccessResponse($result, 'success');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return CommonUtility::getErrorResponse("Get list admin notifications fail");
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Admin\AdminFormRequest $request
     */
    public function store(AdminFormRequest $request)
    {
        try {
            $data = [
                'data' => [
                    'name' => $request->name,
                    'is_main' => $request->is_main,
                    'is_super_admin' => $request->is_super_admin,
                    'user_name' => $request->user_name,
                    'email' => $request->email,
                    'password' => $request->password,
                ],
                'user' => $request->user(),
                'roles' => $request->roles
            ];
            return CommonUtility::getSuccessResponse($this->service->create($data), "success");
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return CommonUtility::getErrorResponse("Create error");
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     */
    public function show($id)
    {
        try {
            return CommonUtility::getSuccessResponse($this->service->getDetail($id), "success");
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return CommonUtility::getErrorResponse("Get detail fail");
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Admin\AdminFormRequest $request
     * @param  int  $id
     * @return mixed
     */
    public function update(AdminFormRequest $request, $id)
    {
        try {
            $data = [
                'id' => $id,
                'data' => [
                    'name' => $request->name,
                    'password' => $request->password,
                    'email' => $request->email,
                    'user_name' => $request->user_name,
                ],
                'roles' => $request->roles
            ];
            return CommonUtility::getSuccessResponse($this->service->update($data), "success");
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return CommonUtility::getErrorResponse("Update error");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy($id)
    {
        try {
            return CommonUtility::getSuccessResponse($this->service->delete($id), "Delete success");
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return CommonUtility::getErrorResponse("Delete error");
        }
    }

    public function me(Request $request)
    {
        try {
            $user = $request->user();
            $user->listPermission = $user->getAllPermissions()->pluck('group');
            if ($teacher = $user->getTeacherIfExists()) {
                $user->teacher = $teacher;
            }
            return CommonUtility::getSuccessResponse($user, "Get user success");
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return CommonUtility::getErrorResponse("Get user error");
        }
    }

    public function changePassword(Request $request)
    {
        try {
            return CommonUtility::getSuccessResponse($this->service->changePassword($request->all()), "Change password success");
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return CommonUtility::getErrorResponse("Change password error");
        }
    }
}
