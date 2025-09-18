<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\UserLogService;
use Illuminate\Http\Request;
use App\Core\CommonUtility;
use Exception;
use Illuminate\Support\Facades\Log;

class UserLogController extends Controller
{
    protected $userLogService;

    public function __construct(UserLogService $userLogService)
    {
        $this->userLogService = $userLogService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $limit = $request->input('limit', 10);
            $page = $request->input('page', 1);
            $filters = [
                'full_name' => $request->input('full_name'),
                'course_title' => $request->input('course_title'),
                'lesson_title' => $request->input('lesson_title'),
            ];
            return CommonUtility::getSuccessResponse(
                $this->userLogService->getListUserLog($limit, $page, $filters), "success"
            );
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return CommonUtility::getErrorResponse("Get list user logs fail");
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            return CommonUtility::getSuccessResponse($this->userLogService->create($request->all()), "success");
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return CommonUtility::getErrorResponse("Save user log fail");
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            return CommonUtility::getSuccessResponse($this->userLogService->find($id), "success");
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return CommonUtility::getErrorResponse("Get user log detail fail");
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $data = $request->all();
            $data['id'] = $id;
            return CommonUtility::getSuccessResponse($this->userLogService->update($data), "success");
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return CommonUtility::getErrorResponse("Update user log fail");
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            return CommonUtility::getSuccessResponse($this->userLogService->delete($id), "success");
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return CommonUtility::getErrorResponse("Delete user log fail");
        }
    }
}
