<?php

namespace App\Http\Controllers\Admin;

use App\Core\AppConst;
use App\Services\RoleService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoleFormRequest;
use Illuminate\Http\Request;
use App\Core\CommonUtility;
use Exception;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    protected $service;

    public function __construct(RoleService $service)
    {
        $this->service = $service;
        $this->middleware('permission:create_role')->only('store');
        $this->middleware('permission:update_role')->only('update');
        $this->middleware('permission:delete_role')->only('destroy');
        $this->middleware('permission:view_role')->only('show');
        $this->middleware('permission:list_role')->only(['index','search']);
    }

    /**
     * Show list category
     *
     */
    public function index(Request $request)
    {
        try {
            $limit = $request->input('limit', 10);
            $page = $request->input('page', 1);
            return CommonUtility::getSuccessResponse($this->service->getAllPaginate($limit, $page), "success");
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return CommonUtility::getErrorResponse("Get list fail");
        }
    }

    public function show($id)
    {
        try {
            return CommonUtility::getSuccessResponse($this->service->getById($id), "success");
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return CommonUtility::getErrorResponse("Get detail fail");
        }
        
    }

    /**
     * Create category.
     *
     * @param \Illuminate\Http\Request $request
     */
    public function store(RoleFormRequest $request)
    {
        try {
            return CommonUtility::getSuccessResponse($this->service->create($request->all()), "success");
        } catch (Exception $e) {
            return CommonUtility::getErrorResponse("Save role fail: " . $e->getMessage());
        }
        
    }

    /**
     * Update catetory.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     */
    public function update(RoleFormRequest $request, $id)
    {
        try {
            return CommonUtility::getSuccessResponse($this->service->update($request->validated(), $id), "success");
        } catch (Exception $e) {
            return CommonUtility::getErrorResponse("Update role fail: " . $e->getMessage());
        }
    }

    /**
     * Remove category.
     *
     * @param int $id
     */
    public function destroy($id)
    {
        return $this->baseActionTransaction(function() use($id){
            return $this->service->delete($id);
        }, "Delete success", "Delete error");
    }
}
